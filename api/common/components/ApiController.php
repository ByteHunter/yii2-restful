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
        $page = \Yii::$app->getRequest()->getQueryParam('page', null);
        $keys = \Yii::$app->getRequest()->getQueryParam('keys', null);
        
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);
        
        // Output keys only
        if (isset($keys)) {
            $dataProvider->pagination = false;
            return $dataProvider->keys;
        }
        
        // Output not paginated results
        if (empty($page)) {
            $firstPage = $dataProvider->getPagination()->createUrl(0, null, true);
            $dataProvider->pagination = false;
            return [
                'items' => $dataProvider->getModels(),
                'links' => [
                    'paginated' => $firstPage,
                ],
            ];
        }
        
        // Default paginated output
        return [
            'items' => $dataProvider->getModels(),
            'links' => $dataProvider->getPagination()->getLinks(true),
        ];
    }
}