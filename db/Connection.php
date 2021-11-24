<?php
namespace app\db;

use app\helpers\Crypt;
use app\helpers\Strings;
use yii\db\Exception;

class Connection extends \yii\db\Connection
{
    /**
     * @throws Exception
     */
    public function __construct($config = [])
    {
        \Yii::info("Iniciando base de dados", "api");
        return parent::__construct([
            'dsn' => $config["dsn"],
            'username' => $config["username"],
            'password' => Crypt::easyDecrypt(trim(Strings::byteArrayToString($config["password"])),Crypt::getOurSecret()),
            'charset' => $config["charset"],
        ]);
    }
}