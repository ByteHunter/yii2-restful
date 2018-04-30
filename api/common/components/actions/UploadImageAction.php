<?php
namespace api\common\components;

use yii\rest\Action;
use yii\base\InvalidConfigException;
use api\common\models\ImageForm;

class UploadImageAction extends Action
{
    public $imageAttribute;
    
    public function init()
    {
        parent::init();
        if ($this->imageAttribute === null) {
            throw new InvalidConfigException(get_class($this) . '::$imageAttribute must be set');
        }
    }
    
    public function run($id)
    {
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }
        
        $file = \yii\web\UploadedFile::getInstanceByName('file');
        $image = new ImageForm(['imageFile' => $file]);
        if ($image->upload($this->getDirectory($model->id))) {
            $model->{$this->imageAttribute} = $image->src;
            $model->update();
            $model->refresh();
            return $model;
        } else {
            return $image;
        }
    }
    
    protected function getDirectory($modelId)
    {
        return "{$this->modelClass::tableName()}/{$modelId}";
    }
}
