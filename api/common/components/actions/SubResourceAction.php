<?php
namespace api\common\components\actions;

use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\rest\Action;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

class SubResourceAction extends Action
{
    public $relation;

    public function init()
    {
        parent::init();
        if ($this->relation === null) {
            throw new InvalidConfigException(get_class($this) . '::$relation must be set');
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        $expand = \Yii::$app->getRequest()->getQueryParam('expand', false);

        return $this->prepareDataProvider($id);

        $model = $this->findModel($id);
        $rel = $this->relation;
        $pk = $this->getRelationPk($model);

        if (isset($model->$rel)) {
            if (is_array($model->$rel)) {
                return $model->$rel;
            } else {
                return $model->$rel;
            }
        } else {
            throw new NotFoundHttpException();
        }
    }

    public function getRelationPk(ActiveRecord $model) : string
    {
        $this->controller;
        $relationClass = $model->getRelation($this->relation)->modelClass::className();
        $pk = $relationClass::primaryKey()[0];
        return $pk;
    }

    public function transformRelation() : string
    {
        return "get" . ucfirst($this->relation);
    }

    public function prepareFindQuery($id, $relation) : \yii\db\ActiveQuery
    {
        $model = $this->findModel($id);
        $model->load(\Yii::$app->request->queryParams, '');
        $query = $model->{$this->transformRelation()}();
        return $query;
    }

    public function prepareDataProvider($id) : ActiveDataProvider
    {
        $relation = $this->transformRelation();
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $this->prepareFindQuery($id, $relation),
        ]);
        $dataProvider->pagination = false;
        $dataProvider->prepare(true);

        return $dataProvider;
    }
}
