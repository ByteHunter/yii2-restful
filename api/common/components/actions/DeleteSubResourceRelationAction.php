<?php

namespace api\common\components\actions;

use yii\rest\Action;
use yii\base\InvalidConfigException;


class DeleteSubResourceRelationAction extends Action
{
    public $relation;
    private $relatedClass;

    public function init()
    {
        parent::init();
        if ($this->relation === null) {
            throw new InvalidConfigException(get_class($this) . '::$relation must be set');
        }
    }

    public function run($id, $subId = null)
    {
        $model = $this->findModel($id);
        $this->relatedClass = $model->getRelation($this->relation)->modelClass::className();

        if ($subId !== null) {
            return $this->unlink($model, $subId);
        } else {
            return $this->unlinkAll($model);
        }
    }

    private function unlink($model, $id)
    {
        try {
            $relatedModel = $this->relatedClass::findOne($id);
            if ($relatedModel !== null) {
                $model->unlink($this->relation, $relatedModel, true);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function unlinkAll($model)
    {
        try {
            $model->unlinkAll($this->relation, true);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
