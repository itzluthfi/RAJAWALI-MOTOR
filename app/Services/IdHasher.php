<?php

declare(strict_types=1);

namespace App\Services;

class IdHasher
{
    private const SALT = 987654321;
    private const XOR_KEY = 123456789;

    /**
     * Encrypt / Obfuscate integer ID or string into a standardized URL hash.
     */
    public static function encode(int|string|null $id): string
    {
        if ($id === null || $id === '') {
            return '';
        }

        if (is_numeric($id)) {
            $num = (int) $id;
            return strtoupper(dechex(($num * self::SALT) ^ self::XOR_KEY));
        }

        return (string) $id;
    }

    /**
     * Decrypt hash or string back to original integer ID or document code.
     */
    public static function decode(string|int|null $hash): int|string
    {
        if ($hash === null || $hash === '') {
            return 0;
        }

        if (is_numeric($hash)) {
            return (int) $hash;
        }

        if (is_string($hash) && ctype_xdigit($hash)) {
            $val = hexdec($hash);
            $num = ($val ^ self::XOR_KEY) / self::SALT;
            if (is_numeric($num) && floor($num) == $num && $num > 0) {
                return (int) $num;
            }
        }

        return (string) $hash;
    }
}
