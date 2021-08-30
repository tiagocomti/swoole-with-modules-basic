<?php


namespace app\helpers;


class Digest
{
    const REALM= "Lockme - User";
    const PATH_DIGEST = "/data/digest.txt";
    const SCENARIO_SAVE = 'save';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_DELETE = 'delete';


    public static function writeDigest($scenario, $username, $password, $realm = self::REALM, $isHash = false): string {
        $string = $username . ':' . $realm . ':';
        $hash = md5($username . ':' . $realm . ':' . $password);

        if($isHash != false) {
            $string = $username . ':' . $realm . ':';
            $hash = $password;
        }

        $file = \Yii::$app->basePath.self::PATH_DIGEST;

        if ($scenario === 'save'){
            $existingContent = file_get_contents ($file);
            $fp = fopen($file, 'w');
            fwrite($fp, $existingContent);
            fwrite($fp, $string.$hash."\n");
            fclose($fp);
        }else if ($scenario === 'update'){
            $content = file($file);
            foreach ($content as $line_num => $line) {
                $val = explode(":",$line);
                if (false === (strpos($val[0], $username))) continue;
                $content[$line_num] = $string.$hash."\n";
                $found = true;
            }
            if ($found === true){
                file_put_contents($file, $content);
            }else{
                $existingContent = file_get_contents ($file);
                $fp = fopen($file, 'w');
                fwrite($fp, $existingContent);
                fwrite($fp, $string.$hash."\n");
                fclose($fp);
            }
        }else if ($scenario === 'delete'){
            $content = file($file);
            foreach ($content as $line_num => $line) {
                $val = explode(":",$line);
                if (false === (strpos($val[0], $username)) && (strpos($val[0], $hash) === false)) continue;
                $content[$line_num] = "";
            }
            file_put_contents($file, $content);
        }
        return $hash;
    }
}