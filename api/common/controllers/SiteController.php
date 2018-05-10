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
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [HttpBearerAuth::className()],
            'except' => [],
            'optional' => ["index", "status", "error"],
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
            ],
            'denyCallback' => function ($rule, $action) {
                throw new \yii\web\UnauthorizedHttpException("Your request was made with invalid credentials.");
            },
        ];
        return $behaviors;
    }
    
    /**
     * This action shows useful info about this service and what can be done next
     */
    public function actionIndex() : void
    {
        return;
    }
    
    /**
     * Example of status info endpoint
     * @return string[]
     */
    public function actionStatus() : array
    {
        return [
            'status' => 'OK',
        ];
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
