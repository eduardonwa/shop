<?php

namespace App\Helpers;

use Money\Money;

class FormatMoney
{
    public static function format($amount): string
    {
        if ($amount instanceof Money) {
            $value = $amount->getAmount() / 100;
        } else {
            $value = $amount / 100;
        }
        
        return '$ ' . number_format($value, 2, '.', ',');
    }
}