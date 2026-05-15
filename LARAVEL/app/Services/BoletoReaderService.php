<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BoletoReaderService
{
    // ─────────────────────────────────────────────
    //  Public API
    // ─────────────────────────────────────────────

    public function read(UploadedFile $file): array
    {
        $imagePath = $this->toImage($file);

        try {
            // 1. Tenta ler o código de barras linear (CODE-128)
            $barcode = $this->extractBarcode($imagePath);

            // 2. Tenta extrair valor e data via OCR do texto da imagem
            //    (mais confiável para concessionárias como TIM, CEMIG, COPEL etc.)
            $ocrData = $this->ocrExtractTextData($imagePath);

            // 3. Parseia o barcode para dados estruturais
            $parsed = $this->parse($barcode);

            // 4. OCR tem prioridade sobre barcode para valor e data
            //    (barcode de concessionária não codifica valor/data em posição padrão)
            $amount  = $ocrData['amount']   ?? $parsed['amount'];
            $dueDate = $ocrData['due_date'] ?? $parsed['due_date'];

            return [
                'barcode'        => $barcode,
                'linha_digitavel' => $this->toLinhaDigitavel($barcode),
                'amount'         => $amount,
                'due_date'       => $dueDate,
                'bank'           => $parsed['bank'],
                'beneficiary'    => $parsed['beneficiary'],
            ];
        } finally {
            if ($this->isTempPath($imagePath)) {
                @unlink($imagePath);
            }
        }
    }

    // ─────────────────────────────────────────────
    //  Step 1 — Normalize input to image
    // ─────────────────────────────────────────────

    private function toImage(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            return $file->getPathname();
        }

        if ($mime === 'application/pdf') {
            return $this->pdfToImage($file->getPathname());
        }

        throw new RuntimeException("Formato não suportado: {$mime}. Use JPG, PNG ou PDF.");
    }

    private function pdfToImage(string $pdfPath): string
    {
        $outPath = sys_get_temp_dir() . '/boleto_' . uniqid() . '.png';

        if ($this->commandExists('gs')) {
            $cmd = sprintf(
                'gs -dNOPAUSE -dBATCH -sDEVICE=pngalpha -r200 -dFirstPage=1 -dLastPage=1 '
                . '-sOutputFile=%s %s 2>/dev/null',
                escapeshellarg($outPath),
                escapeshellarg($pdfPath)
            );
            exec($cmd, $output, $code);

            if ($code === 0 && file_exists($outPath)) {
                return $outPath;
            }
        }

        if (extension_loaded('imagick') && class_exists('Imagick')) {
            $imagick = new \Imagick();
            $imagick->setResolution(200, 200);
            $imagick->readImage($pdfPath . '[0]');
            $imagick->setImageFormat('png');
            $imagick->writeImage($outPath);
            $imagick->clear();

            if (file_exists($outPath)) {
                return $outPath;
            }
        }

        throw new RuntimeException(
            'Não foi possível converter o PDF. Instale Ghostscript (gs) ou a extensão Imagick.'
        );
    }

    // ─────────────────────────────────────────────
    //  Step 2 — Extract barcode (CODE-128 only, no QR)
    // ─────────────────────────────────────────────

    private function extractBarcode(string $imagePath): string
    {
        // Desabilita QR Code e Code39 para não confundir com o QR Pix
        // que aparece em boletos modernos (TIM, operadoras, bancos digitais)
        if ($this->commandExists('zbarimg')) {
            $cmd = sprintf(
                'zbarimg --raw -q -S qrcode.disable -S code39.disable %s 2>/dev/null',
                escapeshellarg($imagePath)
            );
            exec($cmd, $output, $code);

            $raw = trim(implode('', $output));

            if ($code === 0 && $this->looksLikeBarcode($raw)) {
                return $this->normalizeBarcode($raw);
            }
        }

        // Fallback: OCR para extrair os dígitos do código de barras impresso
        if ($this->commandExists('python3')) {
            $barcode = $this->ocrExtract($imagePath);
            if ($barcode) {
                return $barcode;
            }
        }

        throw new RuntimeException(
            'Nenhum código de barras detectado. Certifique-se de que o boleto está legível e bem enquadrado.'
        );
    }

    private function ocrExtract(string $imagePath): ?string
    {
        if (!$this->commandExists('tesseract')) {
            return null;
        }

        $outBase = sys_get_temp_dir() . '/boleto_ocr_' . uniqid();
        $cmd = sprintf(
            'tesseract %s %s --psm 6 -l por+eng digits 2>/dev/null',
            escapeshellarg($imagePath),
            escapeshellarg($outBase)
        );
        exec($cmd);

        $txtPath = $outBase . '.txt';
        if (!file_exists($txtPath)) {
            return null;
        }

        $text   = file_get_contents($txtPath);
        @unlink($txtPath);

        $digits = preg_replace('/[^0-9]/', '', $text);

        if (preg_match('/\d{44}/', $digits, $m)) {
            return $m[0];
        }

        if (preg_match('/\d{47}/', $digits, $m)) {
            return $this->linhaDigitavelToBarcode($m[0]);
        }

        return null;
    }

    // ─────────────────────────────────────────────
    //  Step 2b — OCR: extrai valor e data do TEXTO da imagem
    //  (essencial para boletos de concessionária como TIM, CEMIG etc.)
    // ─────────────────────────────────────────────

    private function ocrExtractTextData(string $imagePath): array
    {
        $result = [];

        if (!$this->commandExists('tesseract')) {
            return $result;
        }

        $outBase = sys_get_temp_dir() . '/boleto_text_' . uniqid();

        // PSM 3 = auto segmentação de página, melhor para documentos mistos
        $cmd = sprintf(
            'tesseract %s %s --psm 3 -l por 2>/dev/null',
            escapeshellarg($imagePath),
            escapeshellarg($outBase)
        );
        exec($cmd);

        $txtPath = $outBase . '.txt';
        if (!file_exists($txtPath)) {
            return $result;
        }

        $text = file_get_contents($txtPath);
        @unlink($txtPath);

        // ── Valor ──────────────────────────────────────────
        // Padrões: "R$ 49,13" / "R$49.13" / "VALOR R$ 49,13"
        // Pega o maior valor encontrado (evita pegar parcelas menores no corpo)
        if (preg_match_all('/R\$\s*([\d.,]+)/', $text, $matches)) {
            $amounts = array_map(function ($v) {
                // Normaliza: remove pontos de milhar, troca vírgula por ponto
                $v = preg_replace('/\.(?=\d{3}(?:[.,]|$))/', '', $v);
                $v = str_replace(',', '.', $v);
                return (float) $v;
            }, $matches[1]);

            // Filtra valores plausíveis (entre R$1 e R$999.999)
            $amounts = array_filter($amounts, fn($v) => $v >= 1 && $v < 999999);

            if (!empty($amounts)) {
                // Usa o valor que aparece mais vezes (geralmente o "Total" e o cabeçalho batem)
                $counts = array_count_values(array_map(fn($v) => number_format($v, 2), $amounts));
                arsort($counts);
                $mostCommon = array_key_first($counts);
                $result['amount'] = (float) $mostCommon;
            }
        }

        // ── Vencimento ─────────────────────────────────────
        // Padrões: "20/04/2026" / "2026-04-20" / "VENCIMENTO 20/04/2026"
        $datePatterns = [
            '/(?:VENCIMENTO|VENC\.?|DUE\s*DATE)[:\s]*(\d{2}\/\d{2}\/\d{4})/i',
            '/(\d{2}\/\d{2}\/20\d{2})/',   // dd/mm/yyyy com ano 20xx
            '/(\d{4}-\d{2}-\d{2})/',        // yyyy-mm-dd
        ];

        foreach ($datePatterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $raw = $m[1];

                // Converte dd/mm/yyyy para yyyy-mm-dd
                if (preg_match('/(\d{2})\/(\d{2})\/(\d{4})/', $raw, $parts)) {
                    $result['due_date'] = "{$parts[3]}-{$parts[2]}-{$parts[1]}";
                } else {
                    $result['due_date'] = $raw; // já em yyyy-mm-dd
                }

                break;
            }
        }

        return $result;
    }

    // ─────────────────────────────────────────────
    //  Step 3 — Barcode ↔ Linha Digitável
    // ─────────────────────────────────────────────

    private function toLinhaDigitavel(string $barcode): string
    {
        if (strlen($barcode) !== 44) {
            return $barcode;
        }

        // Concessionária (começa com 8) — formato diferente
        if ($barcode[0] === '8') {
            return $this->toLinhaDigitavelConcessionaria($barcode);
        }

        $bank     = substr($barcode, 0, 3);
        $currency = substr($barcode, 3, 1);
        $free     = substr($barcode, 4, 20);
        $checkDig = substr($barcode, 24, 1);
        $due      = substr($barcode, 25, 4);
        $amount   = substr($barcode, 29, 10);

        $f1raw = $bank . $currency . substr($free, 0, 5);
        $f1    = substr($f1raw, 0, 5) . '.' . substr($f1raw, 5) . $this->mod10($f1raw);

        $f2raw = substr($free, 5, 10);
        $f2    = substr($f2raw, 0, 5) . '.' . substr($f2raw, 5) . $this->mod10($f2raw);

        $f3raw = substr($free, 15, 10);
        $f3    = substr($f3raw, 0, 5) . '.' . substr($f3raw, 5) . $this->mod10($f3raw);

        return "{$f1} {$f2} {$f3} {$checkDig} {$due}{$amount}";
    }

    private function toLinhaDigitavelConcessionaria(string $barcode): string
    {
        // Divide o barcode em 4 blocos de 10 dígitos (sem o check geral)
        // e adiciona check individual para cada bloco
        $blocks = str_split($barcode, 10);
        $result = [];

        foreach ($blocks as $block) {
            $result[] = $block . $this->mod10($block);
        }

        return implode(' ', $result);
    }

    private function linhaDigitavelToBarcode(string $linha): string
    {
        $d = preg_replace('/\D/', '', $linha);

        if (strlen($d) !== 47) {
            throw new RuntimeException('Linha digitável inválida (deve ter 47 dígitos).');
        }

        $bank     = substr($d, 0, 3);
        $currency = substr($d, 3, 1);
        $f1free   = substr($d, 4, 5);
        $f2free   = substr($d, 10, 10);
        $f3free   = substr($d, 21, 10);
        $checkDig = substr($d, 32, 1);
        $due      = substr($d, 33, 4);
        $amount   = substr($d, 37, 10);

        $free = $f1free . $f2free . $f3free;

        return $bank . $currency . $free . $checkDig . $due . $amount;
    }

    // ─────────────────────────────────────────────
    //  Step 4 — Parse barcode metadata
    // ─────────────────────────────────────────────

    private function parse(string $barcode): array
    {
        if (strlen($barcode) !== 44) {
            return $this->parseConcessionaria($barcode);
        }

        // Concessionária (começa com 8) — layout diferente
        if ($barcode[0] === '8') {
            return $this->parseConcessionaria($barcode);
        }

        // Banco (começa com 0-7) — layout FEBRABAN padrão:
        // [0:3] banco | [3] moeda | [4:24] campo livre
        // [24] dígito verificador | [25:29] fator vencimento | [29:39] valor
        $bankCode  = substr($barcode, 0, 3);
        $dueFactor = (int) substr($barcode, 25, 4);
        $amountRaw = (int) substr($barcode, 29, 10);

        return [
            'amount'      => $amountRaw > 0 ? $amountRaw / 100.0 : null,
            'due_date'    => $this->factorToDate($dueFactor),
            'bank'        => $bankCode,
            'beneficiary' => null,
        ];
    }

    private function parseConcessionaria(string $barcode): array
    {
        // Para concessionárias (começa com 8), o valor está em [3:13]
        // e vencimento geralmente não está codificado no barcode
        $bankCode  = null;
        $amountRaw = 0;

        if (strlen($barcode) >= 13 && $barcode[0] === '8') {
            $amountRaw = (int) substr($barcode, 3, 10);
        } elseif (strlen($barcode) >= 15) {
            $amountRaw = (int) substr($barcode, 4, 11);
        }

        return [
            'amount'      => $amountRaw > 0 ? $amountRaw / 100.0 : null,
            'due_date'    => null, // extraído via OCR do texto
            'bank'        => $bankCode,
            'beneficiary' => null,
        ];
    }

    private function factorToDate(int $factor): ?string
    {
        if ($factor === 0) {
            return null;
        }

        $base = new \DateTime('1997-10-07');
        $base->modify("+{$factor} days");

        return $base->format('Y-m-d');
    }

    // ─────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────

    private function mod10(string $num): int
    {
        $sum  = 0;
        $mult = 2;

        for ($i = strlen($num) - 1; $i >= 0; $i--) {
            $val  = (int) $num[$i] * $mult;
            $sum += $val > 9 ? $val - 9 : $val;
            $mult = $mult === 2 ? 1 : 2;
        }

        $remainder = $sum % 10;
        return $remainder === 0 ? 0 : 10 - $remainder;
    }

    private function looksLikeBarcode(string $str): bool
    {
        $digits = preg_replace('/\D/', '', $str);
        return in_array(strlen($digits), [44, 47, 48]);
    }

    private function normalizeBarcode(string $raw): string
    {
        $digits = preg_replace('/\D/', '', $raw);

        if (strlen($digits) === 47) {
            return $this->linhaDigitavelToBarcode($digits);
        }

        return $digits;
    }

    private function isTempPath(string $path): bool
    {
        return str_starts_with($path, sys_get_temp_dir() . '/boleto_');
    }

    private function commandExists(string $cmd): bool
    {
        exec('command -v ' . escapeshellarg($cmd) . ' 2>/dev/null', $out, $code);
        return $code === 0;
    }
}