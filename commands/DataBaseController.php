<?php


namespace app\commands;
use app\commands\DefaultController as Controller;
use app\helpers\Crypt;
use app\helpers\Date;
use app\models\User;
use yii\helpers\BaseConsole;
use yii\helpers\Console;

class DataBaseController extends Controller
{
    public function actionCryptPass(){
        $string = (BaseConsole::input("set a 32byte database pass: "));
        $pass = Crypt::easyEncrypt($string, Crypt::getOurSecret());
        $byte_array = unpack('C*', $pass);
        BaseConsole::output($this->ansiFormat("Paste it in your db conf:", Console::FG_GREEN));
        BaseConsole::output(json_encode($byte_array));
    }

    public function actionCheckDb(){
        BaseConsole::output("start at: ". Date::getTimeWithMicroseconds());
        User::findOne([true => true]);
        BaseConsole::output("base ok.");
        BaseConsole::output("end at: ". Date::getTimeWithMicroseconds());
    }
}