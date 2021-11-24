<?php

namespace app\modules\api\modules\v1\controllers;
use yii\web\BadRequestHttpException;

class TesteController extends DefaultController
{
    /**
     * Creating a new user for my sis. Curl example:
     */
    public function actionIndex(){
        return ['return'=>true];
    }
}