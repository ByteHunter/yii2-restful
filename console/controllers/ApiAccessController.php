<?php
namespace console\controllers;


use yii\console\Controller;
use common\models\ApiAccess;

class ApiAccessController extends Controller
{
    /**
     * List all configured api accesses
     */
    public function actionIndex()
    {
        $models = ApiAccess::find()->all();

        $this->stdout(str_pad('ID', '8', ' ', STR_PAD_RIGHT));
        $this->stdout(str_pad('Username', '32', ' ', STR_PAD_RIGHT));
        $this->stdout(str_pad('AccessToken', '40', ' ', STR_PAD_RIGHT));
        $this->stdout(str_pad('Type', '8', ' ', STR_PAD_RIGHT));
        $this->stdout(str_pad('Status', '8', ' ', STR_PAD_RIGHT));
        $this->stdout(PHP_EOL);
        $this->stdout(str_pad('', '100', '-') . PHP_EOL);
        
        $key = -1;
        foreach ($models as $key => $model) {
            $this->stdout(str_pad($model->id, '8', ' ', STR_PAD_RIGHT));
            $this->stdout(str_pad($model->username, '32', ' ', STR_PAD_RIGHT));
            $this->stdout(str_pad($model->access_token, '40', ' ', STR_PAD_RIGHT));
            $this->stdout(str_pad($model->getTypeLabel(), '8', ' ', STR_PAD_RIGHT));
            $this->stdout(str_pad($model->getStatusLabel(), '8', ' ', STR_PAD_RIGHT));
            $this->stdout(PHP_EOL);
        }
        
        $key++;
        $this->stdout(str_pad('', '100', '-') . PHP_EOL);
        $this->stdout("Total: $key models" . PHP_EOL);
    }
    
    /**
     * Creates a new API Access account.
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $type Api account type: client, admin
     * @return number Command exit status
     */
    public function actionCreate($username, $email, $password, $type = 'client')
    {
        $model = new ApiAccess([
            'username' => $username,
            'email' => $email,
        ]);
        $model->setPassword($password);
        if ($type === 'admin') {
            $model->type = ApiAccess::TYPE_ADMIN;
        }
        
        if ($model->save()) {
            $this->stdout("Model saved: " . PHP_EOL);
            $this->stdout(\yii\helpers\VarDumper::dump($model->toArray()) . PHP_EOL);
            return 0;
        } else {
            $this->stdout("Couldn't create new model. Error:" . PHP_EOL);
            $this->stdout(\yii\helpers\VarDumper::dump($model->getErrors()) . PHP_EOL);
            return 1;
        }
    }
    
    /**
     * Shows information about model
     * @param integer $id Model ID
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $this->stdout(\yii\helpers\VarDumper::dump($model->toArray()) . PHP_EOL);
    }
    
    /**
     * Suspends an api user account so it cannot use it's services anymore.
     * @param integer $id Model ID
     * @return number Command exit status
     */
    public function actionSuspend($id)
    {
        $model = $this->findModel($id);
        $model->status = ApiAccess::STATUS_SUSPENDED;
        if ($model->update() !== false) {
            $this->stdout("Model updated." . PHP_EOL);
            $this->stdout(\yii\helpers\VarDumper::dump($model->toArray()) . PHP_EOL);
            return 0;
        } else {
            $this->stdout("Couldn't update model." . PHP_EOL);
            $this->stdout(\yii\helpers\VarDumper::dump($model->getErrors()) . PHP_EOL);
            return 1;
        }
    }
    
    /**
     * Enables an api user so it can authenticate and use api services.
     * @param integer $id Model ID
     * @return number Command exit status
     */
    public function actionEnable($id)
    {
        $model = $this->findModel($id);
        $model->status = ApiAccess::STATUS_ACTIVE;
        if ($model->update() !== false) {
            $this->stdout("Model updated." . PHP_EOL);
            $this->stdout(\yii\helpers\VarDumper::dump($model->toArray()) . PHP_EOL);
            return 0;
        } else {
            $this->stdout("Couldn't update model." . PHP_EOL);
            $this->stdout(\yii\helpers\VarDumper::dump($model->getErrors()) . PHP_EOL);
            return 1;
        }
    }
    
    /**
     * Deletes a model
     * @param integer $id Model ID
     * @return number Command exit status
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete() !== false) {
            $this->stdout("Model was deleted successfuly." . PHP_EOL);
            return 0;
        } else {
            $this->stdout("Couldn't delete any model." . PHP_EOL);
            return 1;
        }
    }
    
    /**
     * Returns data based on the primary key (ID).
     * If the data model is not found, a 404 HTTP exception will be raised.
     * @param string $id The ID of the model to be loaded.
     * @return \common\models\ApiAccess the model found
     * @throws NotFoundHttpException If the model cannot be found
     * @see \yii\rest\Action::findModel()
     */
    protected function findModel($id)
    {
        $model = ApiAccess::findOne($id);
        
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException("Object not found: $id");
        }
    }
}
