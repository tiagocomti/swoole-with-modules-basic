<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'cryptbox' => [
            'class' => 'tiagocomti\cryptbox\Cryptbox',
            //Path to save our keys. Need to set chown www in your root directory
            'keysPath' =>  __DIR__."/../../keys/",
            // Execute: php yii cryptBox/crypt/encode '{Your key}'
            'secret' => '{"1":95,"2":86,"3":65,"4":77,"5":79,"6":83,"7":66,"8":82,"9":73,"10":78,"11":68,"12":65,"13":82,"14":79,"15":72,"16":79,"17":74,"18":69,"19":65,"20":77,"21":65,"22":78,"23":72,"24":65,"25":83,"26":79,"27":65,"28":68,"29":69,"30":85,"31":83,"32":95}',
            // Less security but faster
            'enableCache' => true,
            'timeCache' => 800
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
