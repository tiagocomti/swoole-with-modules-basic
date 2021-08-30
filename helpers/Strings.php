<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\helpers;

/**
 * Description of newPHPClass
 *
 * @author tiago
 */
class Strings {
    
    
    public static function mascarade(string $string = null){
        $string_final = "";
        if($string !== null){
            $ultimas_strings = "";
            $qnt_strings_inicio = 2;
            $qnt_strings_final = -2;
            $len = strlen($string);
            if($len > 15){
                $qnt_strings_inicio = 8;
                $qnt_strings_final = -2;
            }
            else if($len == 1){
                $qnt_strings_inicio = 0;
                $qnt_strings_final = 1;
            }


            $primeiras_letras = substr($string, 0, $qnt_strings_inicio);
            $ultimas_strings = substr($string, $qnt_strings_final);

            $string_final = $primeiras_letras;

            for($i = 1; $i < ($len - $qnt_strings_inicio - (-1 * $qnt_strings_final)); $i++){
                $string_final.= "*";
            }

            $string_final .= $ultimas_strings;
        }
        return $string_final;
    }
    
    public static function mascarade_db(string $string = null){
        $string_final = "";
        if($string !== null){
            $string_final = base64_encode($string);
        }
        return $string_final;
    }
    
    public static function unmask_db(string $string = null){
        $string_final = "";
        if($string !== null){
            $string_final = base64_decode($string);
        }
        return $string_final;
    }
    
    public static function is_email(string $string = null){
        if($string !== null){
            if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
                return true;
            }
        }
        return false;
    }
    
    public static function removeEspecialCharacters($valor){
        $before = array('à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú',"'"," ","\ ","@");
        $after = array('a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U',"","_","_","_");
        $valor = str_replace($before, $after, $valor);
        return $valor;
    }
    
    public static function generateApiUser()
    {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public static function isBinary($str): bool {
        return preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
    }

    public static function getStringBetween($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * @param string $byteArray
     * @return string
     */
    public static function byteArrayToString($byteArray){
        $byte_array = json_decode($byteArray, true);
        return implode(array_map("chr", $byte_array));
    }

    /**
     * @param $string
     * @return string
     */
    public static function hash512($string)
    {
        return hash('sha512', $string);
    }

    public static function hash256($string){
        return hash('sha256', $string);
    }

    public static function array_search_by_key($id, $array, $key = "id") {
        foreach ($array as $k => $val) {
            if ($val[$key] === $id) {
                return $k;
            }
        }
        return null;
    }
}
