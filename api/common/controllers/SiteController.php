<?php
namespace api\common\controllers;

use yii\filters\AccessControl;
use yii\rest\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;

class SiteController extends Controller
{
    /**
     * {@inheritDoc}
     * @see \yii\rest\Controller::behaviors()
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Headers' => ['Authorization', 'Content-Type'],
                'Access-Control-Allow-Credentials' => true,
            ]
        ];
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [HttpBearerAuth::class],
            'except' => [],
            'optional' => ["index", "status", "error", "options", "verify-token"],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ["index"],
                    'roles' => ["?", "@"],
                ],
                [
                    'allow' => true,
                    'actions' => ["status"],
                    'roles' => ["?", "@"],
                ],
                [
                    'allow' => true,
                    'actions' => ["verify-token"],
                    'roles' => ["@"],
                ],
                [
                    'allow' => true,
                    'actions' => ['options'],
                    'roles' => ['?', '@'],
                ],
            ],
            'denyCallback' => function ($rule, $action) {
                throw new \yii\web\UnauthorizedHttpException("Your request was made with invalid credentials.");
            },
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['options'] = [
            'class' => 'api\common\components\actions\OptionsAction',
        ];

        return $actions;
    }

    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['options'] = ['OPTIONS'];
        return $verbs;
    }
    
    /**
     * This action shows useful info about this service and what can be done next
     */
    public function actionIndex() : void
    {
        \Yii::$app->getResponse()->setStatusCode("204");
    }
    
    /**
     * Example of status info endpoint
     */
    public function actionStatus() : void
    {
        \Yii::$app->getResponse()->setStatusCode("204");
    }
    
    /**
     * Error action
     * @return string[]
     */
     public function actionError()
     {
         return [
             'message' => "Error",
         ];
     }

    public function actionVerifyToken() : void
    {
        \Yii::$app->getResponse()->setStatusCode("204");
    }
}
