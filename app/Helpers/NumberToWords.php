<?php

namespace App\Helpers;

class NumberToWords
{
    public static function convert($amount)
    {
        $amount = (float) $amount;
        $taka = floor($amount);
        $paisa = round(($amount - $taka) * 100);

        $words = self::numberToWords($taka) . ' Taka';
        if ($paisa > 0) {
            $words .= ' and ' . self::numberToWords($paisa) . ' Paisa';
        }
        return ucwords(trim($words)) . ' Only';
    }

    private static function numberToWords($number)
    {
        $dictionary = [
            0 => 'zero',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen',
            20 => 'twenty',
            30 => 'thirty',
            40 => 'forty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand',
            100000 => 'lakh',
            10000000 => 'crore'
        ];

        if ($number < 0) {
            return 'negative ' . self::numberToWords(abs($number));
        }

        $string = '';

        if ($number < 21) {
            $string = $dictionary[$number];
        } elseif ($number < 100) {
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= ' ' . $dictionary[$units];
            }
        } elseif ($number < 1000) {
            $hundreds = (int) ($number / 100);
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= ' ' . self::numberToWords($remainder);
            }
        } elseif ($number < 100000) {
            $thousands = (int) ($number / 1000);
            $remainder = $number % 1000;
            $string = self::numberToWords($thousands) . ' ' . $dictionary[1000];
            if ($remainder) {
                $string .= ' ' . self::numberToWords($remainder);
            }
        } elseif ($number < 10000000) {
            $lakhs = (int) ($number / 100000);
            $remainder = $number % 100000;
            $string = self::numberToWords($lakhs) . ' ' . $dictionary[100000];
            if ($remainder) {
                $string .= ' ' . self::numberToWords($remainder);
            }
        } else {
            $crores = (int) ($number / 10000000);
            $remainder = $number % 10000000;
            $string = self::numberToWords($crores) . ' ' . $dictionary[10000000];
            if ($remainder) {
                $string .= ' ' . self::numberToWords($remainder);
            }
        }

        return $string;
    }
}
