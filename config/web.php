<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$current_version = "v1";

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log','api'],
    'modules' => [
        'apiswoole' => [
            'class' => 'app\modules\api\Module',
            'modules' => [
                'v1' => [
                    'class' => 'app\modules\api\modules\v1\Module',
                    'allowedIPs' => ['10.69.64.29'],
                ]
            ],
        ],
        'api' => [
            'class' => 'app\modules\api\Module',
            'modules' => [
                'v1' => [
                    'class' => 'app\modules\api\modules\v1\Module',
                    'allowedIPs' => ['10.69.64.29'],
                ],'v2' => [
                    'class' => 'app\modules\api\modules\v2\Module',
                ],
            ],

        ],
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'XN2GcMPEk6BnF2GNf-ThGwuAo_JyhbHU',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'logFile' => '@app/runtime/logs/api_error.log',
                    'exportInterval' => 1,
                    'maxFileSize' => 1024 * 5,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning'],
                    'logFile' => '@app/runtime/logs/api_warning.log',
                    'maxFileSize' => 1024 * 5,
                    'exportInterval' => 1,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logFile' => '@app/runtime/logs/api_info.log',
                    'exportInterval' => 1,
                    'maxFileSize' => 1024 * 5,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace'],
                    'logFile' => '@app/runtime/logs/api_trace.log',
                    'exportInterval' => 1,
                    'maxFileSize' => 1024 * 5,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'app\logs\FileTarget',
                    'categories' => ['api'],
                    'levels' => ['error', 'warning', 'info'],
                    'logVars' => [],
                    'exportInterval' => 1,
                    'maxFileSize' => 1024 * 5,
                    'maxLogFiles' => 20,
                    'logFile' => '@app/runtime/logs/api.log',
                ],
            ],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'format' => yii\web\Response::FORMAT_JSON,
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    // ...
                ],
            ],
            'charset' => 'UTF-8',
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '<api:(api|apiswoole)>/<module:(v1|v2)>/<controller:\w+>/<action:\w+>' => 'api/<module>/<controller>/<action>',
                '<api:(api|apiswoole)>/<module:\w+>/<controller:\w+>/<action:\w+>' => 'api/'.$current_version.'/<controller>/<action>',
                '<api:(api|apiswoole)>/<controller:\w+>/<action:\w+>' => 'api/'.$current_version.'/<controller>/<action>',
                '<api:(api|apiswoole)>/<controller:\w+>/' => 'api/'.$current_version.'/<controller>/',
            ]
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
