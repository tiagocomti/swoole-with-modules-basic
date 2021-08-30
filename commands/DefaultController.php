<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\commands;

use yii\console\Controller;
/**
 * Description of DefaultController
 *
 * @author tiago
 */
class DefaultController extends Controller {
    
    public function behaviors(){
        $behaviors = parent::behaviors();
        return $behaviors;
    }
    
}
