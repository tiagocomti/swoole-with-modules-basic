<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\helpers;

use phpDocumentor\Reflection\Types\Self_;

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

    public static function sanitizationCPFCNPJ($cpf){
        $before = array('-','.','\\',"/");
        $cpf = str_replace($before, "", $cpf);
        return self::removeEspecialCharacters($cpf);
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
     * @param $string
     * @return string
     */
    public static function hash512($string)
    {
        return hash('sha512', $string);
    }

    public static function isMobile(){
        $useragent=$_SERVER['HTTP_USER_AGENT'];

        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
            return true;
        }
        return false;


    }

    public static function stringToByteArray($string){
        return unpack('C*', $string);
    }

    /**
     * @param string $byteArray
     * @return string
     */
    public static function byteArrayToString(string $byteArray): string{
        $byte_array = json_decode($byteArray, true);
        return implode(array_map("chr", $byte_array));
    }

    public static function isAppAgf(){
        return (strpos($_SERVER['HTTP_USER_AGENT'],"gonative") !== false);
    }

    public static function moneyToFloat($valor){
        $val = str_replace("R$","",$valor);
        $val = str_replace(",",".",$val);
        return trim(preg_replace('/\.(?=.*\.)/', '', $val));
    }

    public static function floatToMoney($price){
        if($price == ""){
            return "";
        }
        return number_format($price, 2, ",", ".");
    }

}
