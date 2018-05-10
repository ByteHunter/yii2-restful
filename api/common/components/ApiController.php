<?php
namespace api\common\components;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\UnauthorizedHttpException;

/**
 * 
 * @author Rostislav Pleshivtsev Oparina
 * @link bytehunter.net
 *
 */
class ApiController extends ActiveController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Headers' => ['Authorization', 'Content-Type'],
                'Access-Control-Allow-Credentials' => true,
            ]
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => [],
            'optional' => ["options", "index", "view"],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'only' => [],
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['index', 'view'],
                    'roles' => ['?', '@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create', 'update', 'delete'],
                    'roles' => ["@"],
                ],
                [
                    'allow' => true,
                    'actions' => ['options'],
                    'roles' => ['?', '@'],
                ],
            ],
            'denyCallback' => function($rule, $action) {
                throw new UnauthorizedHttpException("Your request was made with invalid credentials.");
            },
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        $verbs = parent::verbs();
        $verbs['options'] = ['OPTIONS'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['prepareDataProvider'] = [$this, 'prepareIndexDataProvider'];
        $actions['options'] = [
            'class' => 'api\common\components\actions\OptionsAction',
        ];

        return $actions;
    }

    public function getTableName() : string
    {
        return (new $this->modelClass)::tableName();
    }

    public function findModel(int $id) : ActiveRecord
    {
        $modelClass = $this->modelClass;
        $model = $modelClass::findOne($id);
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException();
        }
    }

    public function loadModel() : ActiveRecord
    {
        /**
         * @var $model ActiveRecord
         */
        $model = new $this->modelClass;
        $model->load(\Yii::$app->request->queryParams, '');
        return $model;
    }

    public function applyFilter(ActiveQuery $query) : ActiveQuery
    {
        $filter = \Yii::$app->getRequest()->getQueryParam('filter', null);
        $table = $this->getTableName();

        if ($filter === null) {
            return $query;
        }

        foreach ($filter as $attribute => $conditions) {
            if (is_array($conditions)) {
                foreach ($conditions as $condition => $value) {
                    $query->andFilterWhere([$condition, "$table.$attribute", $value]);
                }
            } else {
                $query->andFilterWhere(["like", "$table.$attribute", $conditions]);
            }
        }

        return $query;
    }

    public function applyOrder(ActiveQuery $query) : ActiveQuery
    {
        $order = \Yii::$app->getRequest()->getQueryParam('order', null);
        $table = $this->getTableName();

        if ($order === null) {
            return $query;
        }

        if (strpos($order, ".") !== false) {
            [$table, $order] = explode(".", $order);
        }

        if ($order !== null) {
            $query->orderBy([
                str_replace("-", "", "$table.$order") =>
                    strpos("$table.$order", "-") !== false ? SORT_DESC : SORT_ASC
            ]);
        }

        return $query;
    }

    public function prepareFindQuery() : ActiveQuery
    {
        $model = $this->loadModel();
        $query = $model->find();

        $query = $this->applyFilter($query);
        $query = $this->applyOrder($query);

        return $query;
    }

    public function prepareIndexDataProvider()
    {
        $page = \Yii::$app->getRequest()->getQueryParam('page', null);
        $keys = \Yii::$app->getRequest()->getQueryParam('keys', null);
        $ifNoneMatch = \Yii::$app->request->headers->get('if-none-match', null);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $this->prepareFindQuery(),
        ]);
        $ETag = md5(serialize($this->serializeData($dataProvider)));
        \Yii::$app->response->headers->set("etag", $ETag);

        // Output keys only
        if (isset($keys)) {
            $dataProvider->pagination = false;
            $dataProvider->prepare(true);
            return $dataProvider->keys;
        }

        if (isset($ifNoneMatch)) {
            if ($ETag === $ifNoneMatch) {
                \Yii::$app->response->setStatusCode(304);
                return null;
            }
        }

        // Output not paginated results
        if (empty($page)) {
            $firstPage = $dataProvider->getPagination()->createUrl(0, null, true);
            $dataProvider->pagination = false;
            $dataProvider->prepare(true);
            return [
                'items' => $this->serializeData($dataProvider),
                '_links' => [
                    'self' => \yii\helpers\Url::toRoute([''], true),
                    'paginated' => $firstPage,
                ],
            ];
        }

        // Default paginated output
        return [
            'items' => $this->serializeData($dataProvider),
            '_links' => $dataProvider->getPagination()->getLinks(true),
        ];
    }
}
