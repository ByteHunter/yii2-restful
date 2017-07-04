<?php
namespace console\controllers;


use yii\console\Controller;

class AdminController extends Controller
{
    /**
     * @return number
     */
    public function actionIndex()
    {
        return 0;
    }
    
    /**
     * Create a new admin.
     * @param string $username
     * @param string $email This parameter will be used to log in inside control panel.
     * @param string $password
     * @return number
     */
    public function actionCreate($username, $email, $password)
    {
        $model = new \common\models\Admin([
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ]);
        
        if ($model->save()) {
            $auth = \Yii::$app->getAuthManager();
            $role = $auth->getRole("admin");
            if ($role !== null) {
                $auth->assign($role, $model->user_id);
            } else {
                $this->stderr("Couldn't assign role 'admin' for {$username}. The role 'admin' ");
                $this->stderr("doesn't exist. Check your database or run the command ");
                $this->stderr("[php yii db/init] to create all required data.\n");
            }
            
            $this->stdout("Administrator [{$username}] was succesfuly created.\n");
            $this->stdout("Use its email ({$email}) and password to login in control panel.\n");
            return 0;
        } else {
            $this->stderr("Could not create Administrator.\n");
            $this->stderr(\yii\helpers\VarDumper::dump($model->getErrors()));
            return 1;
        }
    }
    
    /**
     * Delete an admin.
     * @param string $email Email that uniquely identifies administrators.
     * @return number
     */
    public function actionDelete($email)
    {
        $model = \common\models\Admin::findOne(['email' => $email]);
        
        if ($model === null) {
            $this->stderr("Sorry, there is no administrator with this email ({$email}).\n");
            return 1;
        }
        
        if (!$this->confirm("Are you sure you want to delete this administrator? ")) {
            return 0;
        }
        
        if ($model->delete() !== false) {
            $this->stdout("Administrator {$email} was deleted.\n");
            return 0;
        } else {
            $this->stderr("Couldn't delete administrator {$email}.\n");
            $this->stderr(\yii\helpers\VarDumper::dump($model->getErrors()));
        }
    }
}
