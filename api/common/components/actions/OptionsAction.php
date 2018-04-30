<?php
namespace api\common\components\actions;

use Yii;


class OptionsAction extends \yii\base\Action
{
    /**
     * @var array the HTTP verbs that are supported by the collection URL
     */
    public $collectionOptions = ['GET', 'PUT', 'PATCH', 'POST', 'DELETE', 'HEAD', 'OPTIONS'];
    /**
     * @var array the HTTP verbs that are supported by the resource URL
     */
    public $resourceOptions = ['GET', 'PUT', 'PATCH', 'POST', 'DELETE', 'HEAD', 'OPTIONS'];
    
    
    /**
     * Responds to the OPTIONS request.
     * @param string $id
     */
    public function run($id = null)
    {
        if (Yii::$app->getRequest()->getMethod() !== 'OPTIONS' && 
            Yii::$app->getRequest()->getMethod() !== 'options') {
        }
        $options = $this->collectionOptions;
        Yii::$app->getResponse()->getHeaders()->set('Access-Control-Allow-Methods', implode(', ', $options));
    }
}
