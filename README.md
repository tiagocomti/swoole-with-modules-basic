<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Yii 2 Basic Project Template with swoole and Modules</h1>
    <br>
</p>

Yii 2 Basic Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
rapidly creating small projects, im using this project for build my API project.

The template contains the basic features including user login/logout and a contact page.
It includes all commonly used configurations that would allow you to focus on adding new
features to your application.

[![Latest Stable Version](https://img.shields.io/packagist/v/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![Total Downloads](https://img.shields.io/packagist/dt/yiisoft/yii2-app-basic.svg)](https://packagist.org/packages/yiisoft/yii2-app-basic)
[![build](https://github.com/yiisoft/yii2-app-basic/workflows/build/badge.svg)](https://github.com/yiisoft/yii2-app-basic/actions?query=workflow%3Abuild)

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      mail/               contains view files for e-mails
      models/             contains model classes
      runtime/            contains files generated during runtime
      modules/            Your direcoty with your modules, im using like this: API -> v1, v2, v3 etc.
      helpers/            A util classes for you, im build this for my encrypt class, Formater string and data etc. I hoppe you guys enjoy
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 7.0.
theses extesions:
~~~
php73-json-7.3.18              The json shared extension for php
php73-pecl-swoole-4.4.18       Asynchronous & concurrent & distributed networking framework
~~~


INSTALLATION
------------

### Install via Composer

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~
git clone https://github.com/tiagocomti/swoole-with-modules-basic.git

cd swoole-with-modules-basic

composer install
~~~

Now you should be able to access the application through the following URL, assuming `swoole-with-modules-basic` is the directory
directly under the Web root.

CONFIGURATION
-------------
### Your secret key
To generate a new secret for make a easy symmetric cryptography use this: ./yii byte-array/encode and write the new secret and paste it here: `config/params.php`.
like this:

```php
return [
'secret' => '{"1":95,"2":95,"3":95,"4":95,"5":95,"6":95,"7":95,"8":95,"9":51,"10":50,"11":98,"12":121,"13":116,"14":101,"15":115,"16":95,"17":110,"18":101,"19":119,"20":95,"21":107,"22":101,"23":121,"24":95,"25":95,"26":95,"27":95,"28":95,"29":95,"30":95,"31":95,"32":95}'
];
```

### Database
after configure the new secret, you have to generate a new password for your database (encrypt one). For this, use it:
`./yii data-base/crypt-pass` and set your 32byte database password. 
Tip: for check your 32bytes pass, use:
``echo ________32bytes_new_key_________ | wc``
the return need be like this:
`1       1      33`

Edit the file `config/db.php` with real data, for example:

```php
<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=127.0.0.1;dbname=mydb',
    'username' => 'root',
    'password' => '{"1":111,"2":99,"3":114,"4":54,"5":71,"6":80,"7":119,"8":52,"9":51,"10":97,"11":97,"12":86,"13":89,"14":115,"15":84,"16":48,"17":83,"18":71,"19":112,"20":90,"21":88,"22":53,"23":70,"24":52,"25":66,"26":104,"27":109,"28":100,"29":100,"30":88,"31":65,"32":68,"33":47,"34":120,"35":57,"36":104,"37":104,"38":104,"39":109,"40":101,"41":85,"42":107,"43":83,"44":55,"45":83,"46":104,"47":116,"48":76,"49":80,"50":49,"51":114,"52":71,"53":49,"54":78,"55":50,"56":116,"57":71,"58":55,"59":121,"60":52,"61":110,"62":78,"63":84,"64":80,"65":105,"66":120,"67":101,"68":117,"69":50,"70":97,"71":79,"72":112,"73":51,"74":85,"75":100,"76":81,"77":113,"78":117,"79":115,"80":66,"81":104,"82":89,"83":72,"84":49,"85":50,"86":113,"87":99,"88":105,"89":66,"90":85,"91":114,"92":57,"93":65,"94":100,"95":111,"96":67}',
    'charset' => 'utf8',
];

```
### Swoole
To start your own swoole server make sure you server doest use the port 9500. if is in use change the `config/server.php` port tag for a randon port you like

now, run `./yii swoole/start` for start swoole, if you need restart (after any changes you make in your project) run `./yii swoole/restart`. And if you want to stop, run `./yii swoole/stop`.

### Modules
At Modules directory we have API module and inside we have v1. you can create any modules you want, just make sure config in your `config/web.php` like this:

```php
'modules' => [
        'api' => [
            'class' => 'app\modules\api\Module',
            'modules' => [
                'v1' => [
                    'class' => 'app\modules\api\modules\v1\Module',
                ],'v2' => [
                    'class' => 'app\modules\api\modules\v2\Module',
                ],
            ],

        ],
]
```

**NOTES:**
- Yii won't create the database for you, this has to be done manually before you can access it.
- Check and edit the other files in the `config/` directory to customize your application as required.
- Refer to the README in the `tests` directory for information specific to basic application tests.


If I can make a person's life easier, I'll be happy!