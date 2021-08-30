<?php

namespace app\models\base;

use app\helpers\Crypt;
use yii\db\ActiveRecord as YiiActiveRecord;
use yii\db\Connection;
use app\helpers\Strings;

class ActiveRecord extends YiiActiveRecord
{
    private static $instance_db = null;
    public static $timeout = "30";

    public static function getDb(): Connection{
        /**
         * Forma antigo de se fazer. agora, a gente pega o DB via header, quando é passado.
         * return Yii::$app->db_user; or return Pgsql::getDb();
         */
        if (self::$instance_db == null) {
            self::$instance_db = new Connection([
                'dsn' => \Yii::$app->db->dsn,
                'username' => \Yii::$app->db->username,
                'password' => Crypt::easyDecrypt(trim(Strings::byteArrayToString(\Yii::$app->db->password)),Crypt::getOurSecret()),
                'charset' => \Yii::$app->db->charset,
                'attributes' => [\PDO::ATTR_TIMEOUT => self::$timeout],
            ]);
            try {
                self::$instance_db->open(); // not sure if this is necessary at this point
            } catch (\yii\db\Exception $e) {
                throw new \Exception($e->getMessage(), $e->getCode());
            }
        }
        return self::$instance_db;
    }
}