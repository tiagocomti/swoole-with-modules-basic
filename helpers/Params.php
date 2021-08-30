<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\helpers;

/**
 *
 * @author tiago
 */
class Params {
    
    /**
     * 
     * @param array $dados dados do usuário, vindo do banco de dados
     * @param array $params array de chave e valor, onde a chave é a mesma chave do array de dados e o valor desse array representa o label que será transformado
     * @return array
     */
    public static function getArrayByParams(array $dados, array $params){
        $array_dados = [];
        
        foreach($dados as $key => $value){
            foreach($params as $columnDB => $label){
                if(isset($value[$columnDB])){
                    $array_dados[$key][$label] = trim((is_array($value[$columnDB])) ? implode("", $value[$columnDB]): $value[$columnDB]);
                }else if(isset($value[strtolower($columnDB)])){
                    $array_dados[$key][$label] = trim((is_array($value[strtolower($columnDB)])) ? implode("", $value[strtolower($columnDB)]): $value[strtolower($columnDB)]);
                }
            }
        }
        return $array_dados;
    }
    
    public static function getColumnsByMapping($mapping){
        foreach($mapping as $chave => $valor){
            if($valor["type_id"] !== \app\models\Type::TYPE_UNTREATABLE){
                $column["column_db"][] = $valor["columndb"];
                $column["labels"][] = $valor["columnlabel"];
                $column["aliases"][$valor["columndb"]] = $valor["columnlabel"];
            }
        }
        return $column;
    }
    
    /**
     * 
     * @param string $configuration
     * @param boolean $returnObject
     * @return object settings|sentence
     * 
     */
    public static function decodeConfigurationDB($configuration, $returnObject = true): ?Object{
        $sentence = "";
        $settings = [];
        $obj = json_decode(Crypt::basicDecryption($configuration));
        if(isset($obj->settings)){$settings = $obj->settings;}
        if(isset($obj->sentence)){$sentence = $obj->sentence;}
        if($returnObject === false){
            return ['settings'=> $settings, 'sentence' => $sentence];
        }else{
            $obj_return = (object)['settings'=> $settings, 'sentence' => $sentence];
            return $obj_return;
        }
    }
    
}
