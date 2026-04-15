<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BoletoReaderService
{
    // ─────────────────────────────────────────────
    //  Public API — single entry point for all sources
    // ─────────────────────────────────────────────

    /**
     * Process any supported file (image or PDF) and return parsed boleto data.
     *
     * @param  UploadedFile  $file
     * @return array
     * @throws RuntimeException
     */
    public function read(UploadedFile $file): array
    {
        $imagePath = $this->toImage($file);

        try {
            $barcode = $this->extractBarcode($imagePath);
            $linhaDigitavel = $this->toLinhaDigitavel($barcode);
            $parsed = $this->parse($barcode);

            return [
                'barcode'        => $barcode,
                'linha_digitavel' => $linhaDigitavel,
                'amount'         => $parsed['amount'],
                'due_date'       => $parsed['due_date'],
                'bank'           => $parsed['bank'],
                'beneficiary'    => $parsed['beneficiary'],
            ];
        } finally {
            // Clean up any temp files we created
            if ($this->isTempPath($imagePath)) {
                @unlink($imagePath);
            }
        }
    }

    // ─────────────────────────────────────────────
    //  Step 1 — Normalize input to image path
    // ─────────────────────────────────────────────

    private function toImage(UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            // Already an image — use the uploaded path directly (no copy needed)
            return $file->getPathname();
        }

        if ($mime === 'application/pdf') {
            return $this->pdfToImage($file->getPathname());
        }

        throw new RuntimeException("Formato não suportado: {$mime}. Use JPG, PNG ou PDF.");
    }

    /**
     * Convert first page of PDF to a PNG using Ghostscript (gs) or Imagick.
     * Falls back to pdfimages if neither is available.
     */
    private function pdfToImage(string $pdfPath): string
    {
        $outPath = sys_get_temp_dir() . '/boleto_' . uniqid() . '.png';

        // Try Ghostscript first (most common on Linux servers)
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

        // Try Imagick PHP extension
        if (extension_loaded('imagick')) {
            $imagick = new \Imagick();
            $imagick->setResolution(200, 200);
            $imagick->readImage($pdfPath . '[0]'); // first page only
            $imagick->setImageFormat('png');
            $imagick->writeImage($outPath);
            $imagick->clear();

            if (file_exists($outPath)) {
                return $outPath;
            }
        }

        throw new RuntimeException(
            'Não foi possível converter o PDF para imagem. '
            . 'Certifique-se de que o Ghostscript (gs) ou a extensão Imagick estão instalados.'
        );
    }

    // ─────────────────────────────────────────────
    //  Step 2 — Extract barcode from image
    // ─────────────────────────────────────────────

    /**
     * Use zxing-cpp (zbarimg) to read barcodes from the image.
     * Falls back to manual OCR-based extraction when zbarimg is unavailable.
     */
    private function extractBarcode(string $imagePath): string
    {
        // Primary: zbarimg (apt install zbar-tools)
        if ($this->commandExists('zbarimg')) {
            $cmd = sprintf(
                'zbarimg --raw -q %s 2>/dev/null',
                escapeshellarg($imagePath)
            );
            exec($cmd, $output, $code);

            $raw = trim(implode('', $output));

            if ($code === 0 && $this->looksLikeBarcode($raw)) {
                return $this->normalizeBarcode($raw);
            }
        }

        // Secondary: pytesseract via Python (best-effort OCR on printed linha digitável)
        if ($this->commandExists('python3')) {
            $barcode = $this->ocrExtract($imagePath);
            if ($barcode) {
                return $barcode;
            }
        }

        throw new RuntimeException(
            'Nenhum código de barras detectado na imagem. '
            . 'Certifique-se de que o boleto está legível e bem enquadrado.'
        );
    }

    /**
     * OCR fallback: extract digits from the image and look for a barcode pattern.
     * Requires tesseract (apt install tesseract-ocr).
     */
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

        $text = file_get_contents($txtPath);
        @unlink($txtPath);

        // Remove all non-digits and spaces/dots
        $digits = preg_replace('/[^0-9]/', '', $text);

        // Brazilian boleto barcode is 44 digits
        if (preg_match('/\d{44}/', $digits, $m)) {
            return $m[0];
        }

        // Try to find a linha digitável (47 digits with dots removed)
        if (preg_match('/\d{47}/', $digits, $m)) {
            return $this->linhaDigitavelToBarcode($m[0]);
        }

        return null;
    }

    // ─────────────────────────────────────────────
    //  Step 3 — Convert barcode ↔ linha digitável
    // ─────────────────────────────────────────────

    /**
     * Convert a 44-digit barcode to the 47-digit "linha digitável".
     *
     * Boleto layout (44 digits):
     *   [3 bank][1 currency][20 free field][1 check digit][4 due date factor][10 amount]
     *
     * Linha digitável (47 digits, shown on the slip):
     *   Field 1 (10): bank(3) + currency(1) + free[0..4](5) + check1 + .
     *   Field 2 (11): free[5..14](10) + check2 + .
     *   Field 3 (11): free[15..24](10) + check3 + .
     *   Field 4 (1):  barcode check digit
     *   Field 5 (14): due date factor(4) + amount(10)
     */
    private function toLinhaDigitavel(string $barcode): string
    {
        if (strlen($barcode) !== 44) {
            return $barcode; // concessionária / non-bank: return as-is
        }

        $bank     = substr($barcode, 0, 3);
        $currency = substr($barcode, 3, 1);
        $free     = substr($barcode, 4, 20);
        $checkDig = substr($barcode, 32, 1);
        $due      = substr($barcode, 33, 4);
        $amount   = substr($barcode, 37, 10);

        // Field 1: bank + currency + free[0..4]
        $f1raw = $bank . $currency . substr($free, 0, 5);
        $f1    = substr($f1raw, 0, 5) . '.' . substr($f1raw, 5) . $this->mod10($f1raw);

        // Field 2: free[5..14]
        $f2raw = substr($free, 5, 10);
        $f2    = substr($f2raw, 0, 5) . '.' . substr($f2raw, 5) . $this->mod10($f2raw);

        // Field 3: free[15..24]
        $f3raw = substr($free, 15, 10);
        $f3    = substr($f3raw, 0, 5) . '.' . substr($f3raw, 5) . $this->mod10($f3raw);

        return "{$f1} {$f2} {$f3} {$checkDig} {$due}{$amount}";
    }

    /**
     * Convert a 47-digit linha digitável back to a 44-digit barcode.
     */
    private function linhaDigitavelToBarcode(string $linha): string
    {
        // Strip everything except digits
        $d = preg_replace('/\D/', '', $linha);

        if (strlen($d) !== 47) {
            throw new RuntimeException('Linha digitável inválida (deve ter 47 dígitos).');
        }

        $bank     = substr($d, 0, 3);
        $currency = substr($d, 3, 1);
        $f1free   = substr($d, 4, 5);   // free[0..4]
        // skip check digit at pos 9
        $f2free   = substr($d, 10, 10); // free[5..14]
        // skip check digit at pos 20
        $f3free   = substr($d, 21, 10); // free[15..24]
        // skip check digit at pos 31
        $checkDig = substr($d, 32, 1);
        $due      = substr($d, 33, 4);
        $amount   = substr($d, 37, 10);

        $free = $f1free . $f2free . $f3free;

        return $bank . $currency . $free . $checkDig . $due . $amount;
    }

    // ─────────────────────────────────────────────
    //  Step 4 — Parse boleto metadata from barcode
    // ─────────────────────────────────────────────

    private function parse(string $barcode): array
    {
        if (strlen($barcode) !== 44) {
            // Concessionária / utility bill (48 digits, different layout)
            return $this->parseConcessionaria($barcode);
        }

        $bankCode  = substr($barcode, 0, 3);
        $dueFactor = (int) substr($barcode, 33, 4);
        $amountRaw = (int) substr($barcode, 37, 10);

        return [
            'amount'      => $amountRaw > 0 ? $amountRaw / 100.0 : null,
            'due_date'    => $this->factorToDate($dueFactor),
            'bank'        => $bankCode,
            'beneficiary' => null, // not encoded in barcode; requires bank lookup
        ];
    }

    private function parseConcessionaria(string $barcode): array
    {
        // Concessionárias use a 48-digit code with a different amount position
        $amountRaw = strlen($barcode) >= 15 ? (int) substr($barcode, 4, 11) : 0;

        return [
            'amount'      => $amountRaw > 0 ? $amountRaw / 100.0 : null,
            'due_date'    => null,
            'bank'        => null,
            'beneficiary' => null,
        ];
    }

    /**
     * The "fator de vencimento" is the number of days since 1997-10-07.
     */
    private function factorToDate(int $factor): ?string
    {
        if ($factor === 0) {
            return null; // no due date
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
        $sum = 0;
        $mult = 2;

        for ($i = strlen($num) - 1; $i >= 0; $i--) {
            $val = (int) $num[$i] * $mult;
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

        // 47-digit linha digitável → convert to 44-digit barcode
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
        exec("command -v " . escapeshellarg($cmd) . " 2>/dev/null", $out, $code);

        return $code === 0;
    }
}
