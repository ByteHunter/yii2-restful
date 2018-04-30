<?php

namespace api\common\components\actions;

use yii\rest\Action;
use yii\base\InvalidConfigException;

class AddSubResourceRelationAction extends Action
{
    public $relation;
    private $requestedId = false;
    private $relatedClass;

    public function init()
    {
        parent::init();
        if ($this->relation === null) {
            throw new InvalidConfigException(get_class($this) . '::$relation must be set');
        }
    }

    public function run($id)
    {
        $model = $this->findModel($id);
        $this->relatedClass = $model->getRelation($this->relation)->modelClass::className();
        $pk = $this->getRelationPk();

        $this->requestedId = \Yii::$app->request->post($pk, false);

        if ($this->requestedId) {
            return $this->linkResource($model, $this->requestedId);
        }
    }

    private function linkResource($model, $id)
    {
        try {
            $relatedModel = $this->relatedClass::findOne($id);
            if ($relatedModel !== null) {
                $model->link($this->relation, $relatedModel);
            }
        } catch (\Exception $e) {
        }
    }

    public function getRelationPk() : string
    {
        $pk = $this->relatedClass::primaryKey()[0];
        return $pk;
    }
}
