<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\logs;
use app\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;

/**
 * Description of FileTarget
 *
 * @author tiago
 */
class FileTarget extends \yii\log\FileTarget{
    const CATEGORY_COMMAND_FETCH = "command";
    const CATEGORY_API = "api";
    const CATEGORY_API_SWOOLE = "apiswoole";



    public $enableRotation = true;
    /**
     * @var int maximum log file size, in kilo-bytes. Defaults to 10240, meaning 10MB.
     */
    public $maxFileSize = 10240; // in KB
    /**
     * @var int number of log files used for rotation. Defaults to 5.
     */
    public $maxLogFiles = 5;

    public function init() {
        parent::init();
    }

    /**
     *
     * @param type $message
     * @return string
     * Quero deixar mais proximo disso:
     * 2019-11-08 10:20:53 apimfa 127.0.0.1 33299a06-48dd-4894-8c9d-cdde3a81eb35 - user find["uid","33299a06-48dd-4894-8c9d-cdde3a81eb35"] - 1
     */
    public function formatMessage($message): string {

        $deep_info = "n/a";
        list($text, $level, $category, $timestamp) = $message;
        $level = \yii\log\Logger::getLevelName($level);
        ;
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Throwable || $text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }
        if($category === self::CATEGORY_COMMAND_FETCH){
            $deep_info = $this->getInfoCLI();
        }else if($category === self::CATEGORY_API || $category === self::CATEGORY_API_SWOOLE){
            $deep_info = $this->getInfoAPI();
        }
        return "[".$this->getTime($timestamp)."]-[".$level."]-$deep_info-[".$text."]";
    }

    public function getTime($timestamp): string {
        $parts = explode('.', sprintf('%F', $timestamp));
        return date('Y-m-d H:i:s', $parts[0]) . ($this->microtime ? ('.' . $parts[1]) : '');
    }

    private function getInfoCLI(){
        $ssh_connection = "";
        $params = [];
        $user = get_current_user();
        $target = "";
        $params = Yii::$app->getRequest()->getParams();
        if(is_array($params) && count($params) > 0){array_shift($params);}

        if(isset(Yii::$app->controller->id) && isset(Yii::$app->controller->action->id)){
            $target = Yii::$app->controller->id ."/" .Yii::$app->controller->action->id ." " .implode(" ", $params);
        }else if(isset ($_SERVER["argv"])){
            $target = implode(" ", $_SERVER["argv"]);
        }

        if(isset($_SERVER["USER"]) && $_SERVER["LOGNAME"]){
            $user = $_SERVER["USER"] .":". $_SERVER["LOGNAME"];
        }
        if(isset($_SERVER["SSH_CONNECTION"])){
            $ssh_connection = $_SERVER["SSH_CONNECTION"];
        }else{
            $ssh_connection = "crontab";
        }

        return "[".$target."]-[".$user."]-[".$ssh_connection."]";
    }

    private function getInfoAPI(){
        $user = "nobody";
        $remote = "";
        $target = "";
        if(isset(Yii::$app->controller->action->id) && isset(Yii::$app->controller->id)){
            $target = Yii::$app->controller->module->id."/".Yii::$app->controller->id ."/" .Yii::$app->controller->action->id;
        }
        $headers = Yii::$app->getRequest()->getHeaders();
        if (isset($headers['x-dpo-name']) && isset($headers['x-dpo-id'])) {
            $user = $headers['x-dpo-name'] .":". $headers['x-dpo-id'];
        }else if(isset($_SERVER["HTTP_USER_AGENT"]) && isset ($_SERVER["USER"])){
            $user = $_SERVER['USER'] .":". $_SERVER['HTTP_USER_AGENT'];
        }

        if(isset($_SERVER["REMOTE_ADDR"])){
            $remote = $_SERVER["REMOTE_ADDR"];
        }

        if(Yii::$app->user->identity && Yii::$app->user->identity instanceof User){
            $user .= "] [ID - ". Yii::$app->user->identity->getId();
        }


        return "[".$target."]-[".$user."]-[".$remote."]";
    }

}
