<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Module
 *
 * @author tiago
 */
namespace app\modules\api;

use Yii;
use yii\base\BootstrapInterface;
use yii\base\Module as BaseModule;

class Module extends BaseModule implements BootstrapInterface
{
    public $controllerNamespace = 'app\modules\api\controllers';

    public function init()
    {
        parent::init();
        \Yii::configure($this, require(__DIR__ . '/config.php'));
    }

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\api\commands';
        }
    }
}