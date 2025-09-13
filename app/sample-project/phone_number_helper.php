<?php
/*
* 02-04-2024
* @AmeerSuhail
*/

use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;

if (!function_exists('get_phone_code')) {
    
    function get_phone_code($phone_number) {
        try {
            $number = PhoneNumber::parse('+'.$phone_number);
            return [
                // 'region' => $number->getRegionCode(),
                'code' => $number->getCountryCode(),
                'phone' => $number->getNationalNumber()
            ];
        } catch (PhoneNumberParseException $e) {
            log_message('error', 'Phone number parse error: ' . $e->getMessage());
            return [
                'code' => '',
                'phone' => ''
            ];
        }
    }
}

