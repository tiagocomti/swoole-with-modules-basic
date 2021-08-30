<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 
 */

namespace app\helpers;

/**
 * Class responsible for formatting and calculating dates
 *
 * @author Tiago Alexandre
 */
class Date {
    /**
     *
     * Specific datetime method for adding days to another date. 
     * 
     * 
     * @param string $date Date time you will receive additional days
     * @param int $days Numbers of days to add on $date
     * @param string $format php format that will be returned after adding dates. Default will be "Y-m-d"
     * @return string will return the date with the format applied
     * @example date::addDays("2019/10/10", 10, "Y-h-m"); return "2019-10-20"
     */
    public static function addDays($date, $days, $format = "Y-m-d"): string{
        $data = str_replace('/', '-', $date);
        $data = date($format, strtotime($date));
        $horaFim = date($format, strtotime("".$data." + ".$days." days"));
        return $horaFim;
    }
    
    /**
     *
     * Specific datetime method for decrease days to another date.
     *
     * @param \DateTime $date Date time to be decreased
     * @param int $days Numbers of days to decrease on $date
     * @param string $format php format that will be returned after decreasing dates. Default will be "Y-m-d"
     * @return string will return the date with the format applied
     * @example date::addDays("2019/10/10", 9, "Y-h-m"); return "2019-10-10"
     */
    public static function decreaseDays($date, $days, $format = "Y-m-d"){
        $data = str_replace('/', '-', $date);
        $data = date($format, strtotime($date));
        $horaFim = date($format, strtotime("".$data." - ".$days." days"));
        return $horaFim;
    }
    
    /**
     * Method used to differentiate two dates regardless of their format. This method will return an array.
     * 
     * @param string $data1
     * @param string $data2
     * @return array Returns an array the difference between the dates.
     * @example date::diffDates("2019/12/12","12-12-2020"); return Array([seconds] => 00[min] => 00[hours] => 00[days] => 366[month] => 0[years] => 1)
     */
    public static function diffDates($data1, $data2): array{
        $date1 = date_create($data1);
        $date2 = date_create($data2);
        if($data1 == false || $date2 == false){
            return ["seconds" => false, "min" => false, "hours" => false, "days" => false, "month" => false, "years" => false];
        }
        $diff = date_diff($date2,$date1);

        $segundos = (str_pad($diff->format("%s"), 2,"0",STR_PAD_LEFT));
        $min = (str_pad($diff->format("%i"), 2,"0",STR_PAD_LEFT));
        $hors = (str_pad($diff->format("%h"), 2,"0",STR_PAD_LEFT));

        $dias = ($diff->format("%a"));
        $mes = ($diff->format("%m"));
        $anos = ($diff->format("%y"));
        return ["seconds" => $segundos, "min" => $min, "hours" => $hors, "days" => $dias, "month" => $mes, "years" => $anos];
    }
    
    public static function formatDate($data, $formato = "Y-m-d H:i:s"){
        $data = str_replace('/', '-', $data);
        $data = date('Y-m-d H:i:s', strtotime($data));
        $formatada = date($formato, strtotime($data));
        return $formatada;
    }

    public static function isGreater($date1,$date2){
        if(strtotime($date1) > strtotime($date2)){
            return true;
        }else{
            return false;
        }
    }

    public static function getTimeWithMicroseconds() {
        $t = microtime(true);
        $micro = sprintf("%06d", ($t - floor($t)) * 1000000);
        $d = new \DateTime(date('Y-m-d H:i:s.' . $micro, $t));

        return $d->format("Y-m-d H:i:s.u");
    }
}
