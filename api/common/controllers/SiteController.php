<?php
namespace api\common\controllers;

use yii\rest\Controller;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

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
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
                QueryParamAuth::className(),
            ],
        ];
        return $behaviors;
    }
    
    /**
     * This action shows useful info about this service and what can be done next
     * @return string[]
     */
    public function actionIndex()
    {
        return [
            'serviceStatus' => 'Ok',
        ];
    }
    
    /**
     * Example of status info endpoint
     * @return string[]
     */
    public function actionStatus()
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
            'status' => 'Error',
        ];
    }
}