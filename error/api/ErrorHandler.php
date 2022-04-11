<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ErrorHandler
 *
 * @author tiago
 */

namespace app\error\api;

use app\models\User;
use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidParamException;
use yii\base\UserException;
use yii\web\Response;
use Error;
use Exception;
use yii\base\ErrorException;
use yii\base\InvalidRouteException;
use yii\console\Controller;
use yii\console\UnknownCommandException;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use yii\web\UnauthorizedHttpException;

class ErrorHandler extends \yii\web\ErrorHandler{

    const EXCEPTION_UNAUTHORIZED = "Unauthorized";
    const EXCEPTION_BADREQUEST = "BadRequest";
    const EXCEPTION_UNKNOWCLASS = "Unknown Class";
    /**
     * Returns human-readable exception name.
     * @param \Exception $exception
     * @return string human-readable exception name or null if it cannot be determined
     */
    public function getExceptionName($exception)
    {
        if ($exception instanceof \Exception ||
            $exception instanceof InvalidCallException ||
            $exception instanceof InvalidParamException ||
            $exception instanceof \yii\base\UnknownMethodException) {
            if(get_class($exception) == "yii\web\UnauthorizedHttpException"){
                /** @var User $user */
                $user = Yii::$app->user->identity;
                if(isset($user)){
                    $user->logout();
                }
            }
            if(is_callable($exception->getName)) {
                return $exception->getName();
            }else{
                return get_class($exception);
            }

        }

        return null;
    }


    public function handleException($exception) {
        $type_excepetion = $this->getExceptionName($exception);
        Yii::error(json_encode(Yii::$app->request->post()),"api");
        Yii::error("Fail to executed api. Return: ". \GuzzleHttp\json_encode($exception->getMessage())." Code: ". $exception->getCode().", FileLine".$exception->getFile().":".$exception->getLine()." exception_type: ".$type_excepetion."URL: ". $_SERVER["REQUEST_URI"],"api");
        parent::handleException($exception);
    }
}
