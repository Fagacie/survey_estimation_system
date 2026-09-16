<?php

namespace App\Helpers;

class NumberToWords
{
    private static array $ones = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four',
        5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen',
        14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen',
        18 => 'Eighteen', 19 => 'Nineteen',
    ];

    private static array $tens = [
        2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety',
    ];

    /**
     * Convert a numeric amount to words in Ringgit Malaysia format.
     *
     * Examples:
     *   32000.00 → "Ringgit Malaysia Thirty-Two Thousand Only"
     *   45750.50 → "Ringgit Malaysia Forty-Five Thousand Seven Hundred Fifty and Cents Fifty Only"
     */
    public static function convert(float $amount, string $currency = 'MYR'): string
    {
        $prefix = match (strtoupper($currency)) {
            'MYR' => 'Ringgit Malaysia',
            'USD' => 'US Dollars',
            'SGD' => 'Singapore Dollars',
            default => strtoupper($currency),
        };

        $centsLabel = match (strtoupper($currency)) {
            'MYR' => 'Sen',
            'USD' => 'Cents',
            'SGD' => 'Cents',
            default => 'Cents',
        };

        // Split into integer and decimal parts
        $amount = round(abs($amount), 2);
        $integerPart = (int) floor($amount);
        $decimalPart = (int) round(($amount - $integerPart) * 100);

        $words = $prefix . ' ' . self::convertInteger($integerPart);

        if ($decimalPart > 0) {
            $words .= ' and ' . $centsLabel . ' ' . self::convertInteger($decimalPart);
        }

        $words .= ' Only';

        return trim($words);
    }

    /**
     * Convert an integer to its English word representation.
     */
    private static function convertInteger(int $number): string
    {
        if ($number === 0) {
            return 'Zero';
        }

        $parts = [];

        if ($number >= 1_000_000) {
            $millions = (int) floor($number / 1_000_000);
            $parts[] = self::convertBelow1000($millions) . ' Million';
            $number %= 1_000_000;
        }

        if ($number >= 1_000) {
            $thousands = (int) floor($number / 1_000);
            $parts[] = self::convertBelow1000($thousands) . ' Thousand';
            $number %= 1_000;
        }

        if ($number > 0) {
            $parts[] = self::convertBelow1000($number);
        }

        return implode(' ', $parts);
    }

    /**
     * Convert a number below 1000 to words.
     */
    private static function convertBelow1000(int $number): string
    {
        $result = '';

        if ($number >= 100) {
            $hundreds = (int) floor($number / 100);
            $result .= self::$ones[$hundreds] . ' Hundred';
            $number %= 100;
            if ($number > 0) {
                $result .= ' ';
            }
        }

        if ($number >= 20) {
            $ten = (int) floor($number / 10);
            $one = $number % 10;
            $result .= self::$tens[$ten];
            if ($one > 0) {
                $result .= '-' . self::$ones[$one];
            }
        } elseif ($number > 0) {
            $result .= self::$ones[$number];
        }

        return $result;
    }
}
