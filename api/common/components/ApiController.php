<?php
namespace api\common\components;

use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

/**
 * 
 * @author Ross
 *
 */
class ApiController extends ActiveController
{
    /**
     * Checks is the user is Admin or a Client
     * {@inheritDoc}
     * @see \yii\rest\ActiveController::checkAccess()
     */
    public function checkAccess($action, $model = null, $params = [])
    {
        if (in_array($action, ['create', 'update', 'delete'])) {
            if (!\Yii::$app->user->identity->isAdmin()) {
                throw new ForbiddenHttpException(
                    sprintf('Only administrators are allowed to use action [%s]', $action)
                );
            }
        }
    }
    
    public function actions()
    {
        $actions = parent::actions();
    
        $actions['index']['prepareDataProvider'] = [$this, 'prepareIndexDataProvider'];
    
        return $actions;
    }
    
    public function prepareIndexDataProvider()
    {
        $model = new $this->modelClass;
        $model->load(\Yii::$app->request->queryParams, '');
        $query = $model->find();
        $paginated = \Yii::$app->getRequest()->getQueryParam('paginated', 'no');
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);
        if ($paginated === 'no') {
            $dataProvider->pagination = false;
        }
        return $dataProvider;
    }
}