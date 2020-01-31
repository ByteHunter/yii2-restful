<?php
namespace console\controllers;

use common\models\User;
use Exception;
use Yii;
use yii\console\Controller;
use yii\db\StaleObjectException;

/**
 * Class UserController
 * @package console\controllers
 * @author Rostislav Pleshivtsev Oparina
 * @noinspection PhpUnused
 */
class UserController extends Controller
{
    /**
     * @return number
     */
    public function actionIndex()
    {
        return 0;
    }

    /**
     * Create a new user
     * @param string $username
     * @param string $email This parameter will be used to log in inside control panel.
     * @param string $password
     * @param $roleName
     * @return number
     * @noinspection PhpUnused
     */
    public function actionCreate($username, $email, $password, $roleName)
    {
        $model = new User([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'scenario' => User::SCENARIO_CREATE,
        ]);
        if (!$model->save()) {
            $this->stderr("Could not create User.\n");
            $this->stderr(print_r($model->getErrors(), true));
            return 1;
        }
        $this->stdout("User '{$username}' was successfully created, use their email ({$email}) to login.\n");

        $auth = Yii::$app->getAuthManager();
        $role = $auth->getRole($roleName);
        if ($role === null) {
            $this->stderr("Couldn't assign role '{$roleName}' for '{$username}'. The role '{$roleName}' ");
            $this->stderr("doesn't exist. Check your database or run the command ");
            $this->stderr("[php yii db/init] to create all required data.\n");
            return 2;
        }

        try {
            $auth->assign($role, $model->apiAccess->id);
            $this->stderr("Role '{$roleName}' has been assigned to user.\n");
        } catch (Exception $e) {
            $this->stderr("User already has role '{$roleName}'.\n");
        }
        return 0;
    }

    /**
     * Delete a user
     * @param string $email Email that uniquely identifies administrators.
     * @return number
     * @throws StaleObjectException
     * @throws \Throwable
     * @noinspection PhpUnused
     */
    public function actionDelete($email)
    {
        $model = User::findOne(['email' => $email]);
        if ($model === null) {
            $this->stderr("Sorry, there is no user with this email ({$email}).\n");
            return 1;
        }
        
        if (!$this->confirm("Are you sure you want to delete this user? ")) {
            return 0;
        }

        if ($model->delete() !== false) {
            $this->stdout("User '{$email}' was deleted.\n");
            return 0;
        } else {
            $this->stderr("Couldn't delete user '{$email}'.\n");
            $this->stderr(print_r($model->getErrors(), true));
        }
        return 0;
    }

    /**
     * @param $email
     * @param mixed ...$permissions
     * @return int
     * @noinspection PhpUnused
     */
    public function actionAddPermission($email, ...$permissions)
    {
        $model = User::findOne(['email' => $email]);
        if ($model === null) {
            $this->stderr("Sorry, there is no user with this email ({$email}).\n");
            return 1;
        }
        $auth = Yii::$app->getAuthManager();
        foreach ($permissions as $permissionName) {
            $permission = $auth->getPermission($permissionName);
            if ($permission === null) {
                $this->stderr("Couldn't find permission '{$permissionName}.'\n");
                continue;
            }
            try {
                $auth->assign($permission, $model->apiAccess->id);
                $this->stderr("Permission '{$permissionName}' has been assigned to user.\n");
            } catch (Exception $e) {
                $this->stderr("User already has permission '{$permissionName}'.\n");
            }
        }
        return 0;
    }

    /**
     * @param $email
     * @param mixed ...$permissions
     * @return int
     * @noinspection PhpUnused
     */
    public function actionRevokePermission($email, ...$permissions)
    {
        $model = User::findOne(['email' => $email]);
        if ($model === null) {
            $this->stderr("Sorry, there is no user with this email ({$email}).\n");
            return 1;
        }
        $auth = Yii::$app->getAuthManager();
        foreach ($permissions as $permissionName) {
            $permission = $auth->getPermission($permissionName);
            if ($permission === null) {
                $this->stderr("Couldn't find permission '{$permissionName}.'\n");
                continue;
            }
            if ($auth->revoke($permission, $model->apiAccess->id)) {
                $this->stderr("Permission '{$permissionName}' has been revoked from user.\n");
            } else {
                $this->stderr("User doesn't have permission '{$permissionName}'.\n");
            }
        }
        return 0;
    }
}
