<?php

namespace App\Helpers;

class BrasilHelper
{
    // ─── CNPJ ────────────────────────────────────────────────────────────────

    /**
     * Remove tudo que não for dígito.
     */
    public static function normalizeCnpj(?string $cnpj): string
    {
        return preg_replace('/\D/', '', (string) $cnpj);
    }

    /**
     * Formata dígitos para 00.000.000/0000-00.
     * Retorna o valor original se não tiver 14 dígitos.
     */
    public static function formatCnpj(?string $cnpj): string
    {
        $digits = self::normalizeCnpj($cnpj);

        if (strlen($digits) !== 14) {
            return (string) $cnpj;
        }

        return substr($digits, 0, 2) . '.' .
               substr($digits, 2, 3) . '.' .
               substr($digits, 5, 3) . '/' .
               substr($digits, 8, 4) . '-' .
               substr($digits, 12, 2);
    }

    /**
     * Valida estruturalmente o CNPJ (dígitos verificadores).
     */
    public static function validateCnpj(?string $cnpj): bool
    {
        $digits = self::normalizeCnpj($cnpj);

        if (strlen($digits) !== 14) {
            return false;
        }

        // Rejeita sequências idênticas (00000000000000, etc.)
        if (preg_match('/^(\d)\1{13}$/', $digits)) {
            return false;
        }

        // Primeiro dígito verificador
        $sum = 0;
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $digits[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $first = $remainder < 2 ? 0 : 11 - $remainder;

        if ((int) $digits[12] !== $first) {
            return false;
        }

        // Segundo dígito verificador
        $sum = 0;
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $digits[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $second = $remainder < 2 ? 0 : 11 - $remainder;

        return (int) $digits[13] === $second;
    }

    // ─── Telefone ─────────────────────────────────────────────────────────────

    /**
     * Remove tudo que não for dígito.
     */
    public static function normalizePhone(?string $phone): string
    {
        return preg_replace('/\D/', '', (string) $phone);
    }

    /**
     * Formata automaticamente:
     *   11 dígitos → (XX) XXXXX-XXXX  (celular)
     *   10 dígitos → (XX) XXXX-XXXX   (fixo)
     * Retorna o valor original para outros tamanhos.
     */
    public static function formatPhone(?string $phone): string
    {
        $digits = self::normalizePhone($phone);

        if (strlen($digits) === 11) {
            return '(' . substr($digits, 0, 2) . ') ' .
                   substr($digits, 2, 5) . '-' .
                   substr($digits, 7, 4);
        }

        if (strlen($digits) === 10) {
            return '(' . substr($digits, 0, 2) . ') ' .
                   substr($digits, 2, 4) . '-' .
                   substr($digits, 6, 4);
        }

        return (string) $phone;
    }

    /**
     * Verifica se o número de dígitos é válido para BR (10 ou 11).
     */
    public static function validatePhone(?string $phone): bool
    {
        $digits = self::normalizePhone($phone);
        return in_array(strlen($digits), [10, 11], true);
    }
}
