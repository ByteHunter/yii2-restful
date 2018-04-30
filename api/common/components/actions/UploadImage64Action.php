<?php
namespace api\common\components\actions;

use common\models\Image;
use yii\rest\Action;
use yii\base\InvalidConfigException;
use api\common\models\Image64Form;

class UploadImage64Action extends Action
{
    public $imageAttribute;
    public $multipleImage = false;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->imageAttribute === null) {
            throw new InvalidConfigException(get_class($this) . '::$imageAttribute must be set');
        }
    }

    /**
     * @param $id
     * @return Image64Form|\yii\db\ActiveRecordInterface
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $imageForm = new Image64Form();
        $imageForm->load(\Yii::$app->getRequest()->getBodyParams(), "");
        $directory = $this->getDirectory($model->id);

        if ($imageForm->upload($directory, $this->multipleImage)) {
            if (!$this->multipleImage) {
                $model->{$this->imageAttribute} = $imageForm->src;
                $model->update();
                $model->refresh();
            } else {
                $image = new Image();
                $image->path = $imageForm->src;
                $image->type = $this->modelClass::tableName();
                $image->save();
                $image->link($this->modelClass::tableName(), $model);
                return $image;
            }
            return $model;
        } else {
            return $imageForm;
        }
    }

    /**
     * @param $modelId
     * @return string
     */
    protected function getDirectory($modelId)
    {
        return "{$this->modelClass::tableName()}/{$modelId}";
    }
}
