<?php
namespace app\db;

use tiagocomti\cryptbox\Cryptbox;
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
            'password' => Cryptbox::decryptDBPass($config["password"]),
            'charset' => $config["charset"],
        ]);
    }
}