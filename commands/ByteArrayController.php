<?php


namespace app\commands;
use app\commands\DefaultController as Controller;
use yii\helpers\BaseConsole;

class ByteArrayController extends Controller
{
    public function actionEncode($string){
        $byte_array = unpack('C*', $string);
        BaseConsole::output(json_encode($byte_array));
    }
}