<?php

class Converter 
{
    static function convertCurrencies($currencyAmount, $fromCurrencyRatio, $toCurrencyRatio): float
    {     
        return floatval($currencyAmount) * $fromCurrencyRatio  /  $toCurrencyRatio;
    }
}