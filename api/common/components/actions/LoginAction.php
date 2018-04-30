<?php
namespace api\common\components\actions;

use yii\rest\Action;

class LoginAction extends Action
{
    public $scenario = 'login';

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\ForbiddenHttpException
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function run()
    {
        // Fill the form
        $form = new $this->modelClass([
            "scenario" => $this->scenario,
        ]);
        $form->scenario = $form::SCENARIO_LOGIN;
        $form->load(\Yii::$app->request->getBodyParams(), '');
        if (!$form->validate()) {
            throw new \yii\web\UnauthorizedHttpException();
        }

        // Find the model
        $model = $this->getModel($form->email);
        if (!$model->validatePassword($form->password)) {
            throw new \yii\web\ForbiddenHttpException();
        }
        $model->apiAccess->generateToken();
        $model->apiAccess->update();

        return [
            "token" => $model->apiAccess->access_token,
        ];
    }

    /**
     * @param $email
     * @return mixed
     * @throws \yii\web\ForbiddenHttpException
     */
    public function getModel($email)
    {
        $model = $this->modelClass::findOne(["email" => $email]);
        if (isset($model)) {
            return $model;
        } else {
            throw new \yii\web\ForbiddenHttpException();
        }
    }
}
