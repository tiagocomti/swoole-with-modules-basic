<?php

namespace app\modules\api\filters;

use app\models\Mattermost;
use app\models\User;
use Yii;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;
use app\models\DPO;

/**
 * HeaderFilter is an action filter that filters by header.
 *
 * It allows to define allowed HTTP request methods for each action and will throw
 * an HTTP 405 error when the method is not allowed.
 *
 * To use VerbFilter, declare it in the `behaviors()` method of your controller class.
 * For example, the following declarations will define a typical set of allowed
 * request methods for REST CRUD actions.
 *
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'apiKey' => [
 *             'class' => ApiKeyFilter::class,
 *             'authApiKey' => [
 *                 '123123123123123123123',
 *             ],
 *             'header_api_key' => 'X-API-KEY',
 *         ],
 *     ];
 * }
 * ```
 *
 * @see https://tools.ietf.org/html/rfc2616#section-14.7
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class HeaderFilter extends Behavior
{
    public $excludedActions = ["controller/action"];
    /**
     * Declares event handlers for the [[owner]]'s events.
     * @return array events (array keys) and the corresponding event handler methods (array values).
     */
    public function events()
    {
        return [Controller::EVENT_BEFORE_ACTION => 'beforeAction'];
    }

    /**
     * @param ActionEvent $event
     * @return bool
     * @throws UnauthorizedHttpException when the api-key is not allowed.
     * @throws \yii\db\Exception
     */
    public function beforeAction($event)
    {
        $isExclude = false;
        foreach ($this->excludedActions as $valor) {
            $controller_action = explode("/", $valor);
            if ($controller_action[0] === Yii::$app->controller->id) {
                if ($controller_action[1] === Yii::$app->controller->action->id) {
                    $isExclude = true;
                }
            }
        }
        $headers = Yii::$app->getRequest()->getHeaders();
        if (!isset($headers['x-token']) && $isExclude) {
            return true;
        }
        if (!isset($headers['x-token'])) {
            \Yii::error('Heades is missing: x-token'. " locate: ".\Yii::$app->controller->id."/".Yii::$app->controller->action->id, 'api');
            throw new UnauthorizedHttpException('Your request is invalid.');
        }
        if(!$isExclude) {
            $user = User::validateToken($headers['x-token']);
            if ($user === null) {
                \Yii::error('UserID ou username não existe', 'api');
                throw new UnauthorizedHttpException('Your request is invalid.');
            }
            Yii::$app->user->identity = $user;
        }
        return true;
    }
}
