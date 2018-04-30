<?php
namespace api\common\components\actions;

use yii\rest\Action;
use yii\base\InvalidConfigException;

class UpdateSubResourceRelationAction extends Action
{
    public $relation;
    private $currentIdList = [];
    private $requestedIdList = [];
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
        $model->unlinkAll($this->relation, true);
        $this->relatedClass = $model->getRelation($this->relation)->modelClass::className();
        $pk = $this->getRelationPk();
        $rel = $this->relation;

        $this->currentIdList = array_column($model->$rel, $pk);
        $this->requestedIdList = \Yii::$app->request->getBodyParams();

        foreach ($this->requestedIdList as $requestedId) {
            $this->linkResource($model, $requestedId);
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
