<?php

namespace api\modules\v1\controllers;

use api\common\components\ApiController;
use yii\web\UnauthorizedHttpException;

class UserController
    extends ApiController
{
    public $modelClass = 'api\modules\v1\models\User';

    public $createScenario = 'create';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => \yii\filters\HttpCache::class,
            'only' => ['index'],
            'enabled' => true,
        ];
        $behaviors['access']['rules'] = [
            [
                'allow' => true,
                'actions' => ['index', 'view', 'create'],
                'roles' => ['?'],
            ],
            [
                'allow' => true,
                'actions' => ['update', 'delete'],
                'roles' => ['user'],
            ],
            [
                'allow' => true,
                'actions' => ['options'],
                'roles' => ['?', '@'],
            ],
        ];
        $behaviors['authenticator']['except'][] = "create";

        return $behaviors;
    }

    /**
     * @param string $action
     * @param null $model
     * @param array $params
     * @throws UnauthorizedHttpException
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        if (!in_array($action, ['update', 'delete'])) {
            return;
        }
        if (!isset(\Yii::$app->user->identity)) {
            throw new UnauthorizedHttpException();
        }
        $identity = \Yii::$app->user->identity;
        if ($action === 'update' && $identity->isUser() && $identity->user->id !== $model->id) {
            throw new UnauthorizedHttpException();
        }
    }

    public function calculateHash() : string
    {
        $modelClass = $this->modelClass;
        $data = $modelClass::find()->all();
        return md5(serialize($data));
    }
}
