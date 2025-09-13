<?php
/*
* 13-09-2025
* @AmeerSuhail
*/

namespace App\Helpers;

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;

class PhoneNumberHelper
{
    public static function format(?string $code, ?string $phone): string
    {
        if (empty($phone)) {
            return 'N/A';
        }
        $formattedCode = $code ? (str_starts_with($code, '+') ? $code : '+' . $code) : '';
        return trim($formattedCode . ' ' . $phone);
    }

    public static function display(?string $code, ?string $phone): string
    {
        return self::format($code, $phone);
    }

    public static function forCall(?string $code, ?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }
        $formattedCode = $code ? (str_starts_with($code, '+') ? $code : '+' . $code) : '';
        return $formattedCode . $phone;
    }

    public static function get_phone_code($phone_number)
    {
        try {
            $number = PhoneNumber::parse('+'.$phone_number);
            return [
                // 'region' => $number->getRegionCode(),
                'code' => $number->getCountryCode(),
                'phone' => $number->getNationalNumber()
            ];
        } catch (PhoneNumberParseException $e) {
            return [
                'code' => '',
                'phone' => ''
            ];
        }
    }
}


