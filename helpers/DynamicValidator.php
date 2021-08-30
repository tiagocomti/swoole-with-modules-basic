<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\helpers;
use \yii\base\DynamicModel;

/**
 * Class specifies to dynamically validate the model every time an object of type 
 * "Fetch" is created. For each method there is a type of validation, 
 * the idea is that this validation happens dynamically.
 *
 * @author Tiago Alexandre
 */
class DynamicValidator {
    
    const TYPE_PRIMITIVE_STRING = "text";
    const TYPE_PRIMITIVE_PASSWORD = "password";
    const TYPE_PRIMITIVE_BOOL = "bool";
    const TYPE_PRIMITIVE_INT = "number";
    
    /**
     *
     * @var DynamicModel
     */
    public $model;

    /**
     * Validating a conf based on a specific contract.
     * 
     * @param array $settings stdClass Object
    (
        [host] => 127.0.0.1
        [port] => 5432
        [db] => privatum_user_db
    )

     * @param array|object $contract stdClass Object
(
    [host] => stdClass Object
        (
            [type] => text
            [required] => 1
            [limit] => 75
            [label] => Host
        )

    [port] => stdClass Object
        (
            [type] => number
            [required] => 1
            [limit] => 65535
            [label] => Port
        )

    [db] => stdClass Object
        (
            [type] => text
            [required] => 1
            [limit] => 75
            [label] => DataBase Name
        )
)
     * @return bool
     */
    public function validateContract($settings, $contract){
        $obj_contract = (array)$contract;
        $attributes = array_keys($obj_contract);
        $model = new DynamicModel($attributes);
        
        foreach($contract as $chave => $valor){
            $options = [];
            if($valor->type == self::TYPE_PRIMITIVE_STRING || $valor->type == self::TYPE_PRIMITIVE_PASSWORD){
                $type = 'string';
            }else if($valor->type == self::TYPE_PRIMITIVE_BOOL){
                $type = 'boolean';
            }else if($valor->type == self::TYPE_PRIMITIVE_INT){
                $type = 'number';
            }
            if(isset($valor->limit)){
                $options['max'] = $valor->limit;
            }
            if(isset($valor->required) && $valor->required === true){
                $model->addRule($chave, "required");
            }
            
            $model->addRule($chave, $type, $options);
        }
        $array_settings = (array)$settings;
        $model->setAttributes($array_settings);
        $this->model = $model;
        return $model->validate();
    }
    
}
