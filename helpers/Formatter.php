<?php


namespace app\helpers;
use NumberFormatter;


class Formatter
{
    public static function NumberToPercent($number){
        $format = new NumberFormatter('en_US', NumberFormatter::PERCENT);
        return $format->formatCurrency($number/100, 'BRL');
    }
}