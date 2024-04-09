<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class Helper
{
    public static function randomCode($length, $type = 'both'): string
    {
        $key = '';
        $keys = match ($type) {
            'both' => array_merge(range(0, 9), range('a', 'z'), range(0, 9), range('A', 'Z')),
            'string' => array_merge(range('a', 'z'), range('A', 'Z')),
            default => array_merge(range(0, 9)),
        };
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $key;
    }

    public static function normalizePhoneNumber($phoneNumber)
    {
        if (Str::of($phoneNumber)->test('/(0|0098|\+98)9(0[1-5]|[1 3]\d|2[0-2]|98|00)\d{7}/')) {
            if (Str::of($phoneNumber)->test('/(\+98)9\d{9}/')) {
                return "0" . Str::substr($phoneNumber, 3);
            } else {
                return $phoneNumber;
            }
        } else {
            return "";
        }
    }
}