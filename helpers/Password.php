<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace app\helpers;
use Yii;
/**
 * Password helper.
 *
 */
class Password
{
    /**
     * Wrapper for yii security helper method.
     *
     * @param $password
     * @param $hash
     *
     * @return bool
     */
    public static function validate($password, $hash)
    {
        if($password) {
            return Yii::$app->security->validatePassword($password, $hash);
        }
        return false;
    }

    /**
     * Generates user-friendly random password containing at least one lower case letter, one uppercase letter and one
     * digit. The remaining characters in the password are chosen at random from those three sets.
     *
     * @see https://gist.github.com/tylerhall/521810
     *
     * @param $length
     *
     * @param bool $symbol
     * @return string
     */
    public static function generate($length, $symbol = false)
    {
        $sets = [
            'abcdefghjkmnpqrstuvwxyz',
            'ABCDEFGHJKMNPQRSTUVWXYZ',
            '23456789',
        ];
        if($symbol === true){
            $sets[3] = "!@&*()";
        }

        $all = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }
        $all = str_split($all);
        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }
        $password = str_shuffle($password);
        return $password;
    }
    
    public static function generateApiKey($user_digest):string {
        return hash('sha256',"$user_digest".bin2hex(random_bytes(64)));
    }

    public static function saltingHash($hash_salvo_banco, $nonce, $salt_rule){
        if(isset($nonce)){
            $salt = "";
            /*
             * Mesma interação que o frontend conhece
             */
            $iterations = 1000;
            $nonce_array = str_split($nonce);
            //Mesmo passo do front, descobrindo qual é o salt baseado em uma lógioca pré-acordada entre as partes
            $array_regra_salt = explode(",", base64_decode($salt_rule));
            foreach ($array_regra_salt as $value) {
                $salt .= $nonce_array[(int)$value];
            }

            /*
             * Vamos considerar que o hash_salvo_ldap ja foi recuperado com o hash do usuário (que passou, duas vezes pelo processo de sha256) e agora vamos
             * adicionar o salt ao final e aplicar mais um sha256
             */
            $hashValid = hash('sha512',$hash_salvo_banco.$salt);

            /*
             * Mesmo esquema, passei por um pbkdf2 utilizando o mesmo salt (descoberto pela logica pré-acordada pelas partes)
             */
            $hashValid_pbkdf2 = hash_pbkdf2("sha512", $hashValid, $salt, $iterations);

            /**
             * Agora, temos dois hashs iguais. (um gerado pelo front e o outro pelo backend) essa tecnica é muito utilizada quando vamos autenticar o usuário à cada requisição etc.
             */
            return $hashValid_pbkdf2;
        }
    }

    public static function generateSecret($salt = "xxxx-xxxx-xxxx")
    {
        $md5_secret = md5(uniqid(rand(), true));
        return substr(Base32::encode($md5_secret), 0, 32);
    }
}