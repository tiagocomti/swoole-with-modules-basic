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

namespace app\error\console;

use yii;
class ErrorHandler extends yii\console\ErrorHandler{
    public function handleException($exception) {
        \Yii::error("Fail to executed command msg: ".str_replace("\n", "", $exception->getMessage()),"command");
        echo "Fail, check your log file";
        return \yii\console\ExitCode::DATAERR;
    }
}
