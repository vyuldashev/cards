<?php

declare(strict_types=1);

namespace Vyuldashev\Cards;

class Luhn
{
    public static function check(string $number): bool
    {
        $length = mb_strlen($number);
        $sum = 0;
        for ($i = $length - 1; $i >= 0; $i -= 2) {
            $sum += $number[$i];
        }
        for ($i = $length - 2; $i >= 0; $i -= 2) {
            $sum += array_sum(str_split((string)($number[$i] * 2)));
        }

        return $sum % 10 === 0;
    }
}
