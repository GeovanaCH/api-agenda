<?php

namespace App\Support;

final class Cpf
{
    /** Keep digits only: 123.456.789-09 => 12345678909 */
    public static function clean(string $cpf): string
    {
        return preg_replace('/\D/', '', $cpf) ?? '';
    }

    /**
     * Validate length, repeated sequences (111.111.111-11), and check digits.
     */
    public static function isValid(string $cpf): bool
    {
        $cpf = self::clean($cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf) === 1) {
            return false;
        }

        for ($length = 9; $length <= 10; $length++) {
            $sum = 0;

            for ($i = 0; $i < $length; $i++) {
                $sum += (int) $cpf[$i] * ($length + 1 - $i);
            }

            $digit = ($sum * 10) % 11;

            if ($digit === 10) {
                $digit = 0;
            }

            if ($digit !== (int) $cpf[$length]) {
                return false;
            }
        }

        return true;
    }
}
