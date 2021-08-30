<?php


$config = require __DIR__ . '/web.php';
$config['components']['response']['class'] = swoole\foundation\web\Response::class;
$config['components']['request']['class'] = swoole\foundation\web\Request::class;
//$config['components']['errorHandler']['class'] = swoole\foundation\web\ErrorHandler::class;
$config['components']['errorHandler']['class'] = app\error\api\ErrorHandler::class;

$newRules = [];
//foreach ($config['components']['urlManager']['rules'] as $key => $value) {
//    $newRules[preg_replace('/^api(.*)/i','apiswoole${1}',$key)] = $value; //'api/system/index' to 'apiswoole/system/index'
//}
//$config['components']['urlManager']['rules'] = $newRules;

return $config;