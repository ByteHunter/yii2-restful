<?php
namespace console\controllers;

use Exception;
use Yii;
use yii\console\Controller;
use yii\rbac\ManagerInterface;

/**
 * Initializes roles and permissions
 * @package console\controllers
 * @author Rostislav Pleshivtsev Oparina
 * @link bytehunter.net
 */
/** @noinspection PhpUnused */
class InitializeRolesController extends Controller
{
    private $authItems = [];

    private $roles = [
        "user"
    ];

    private $permissions = [
        "User", "User index", "User read", "User create", "User update", "User delete",
        "Superpowers",
    ];

    private $relations = [
        "User" =>   ["User index", "User read", "User create", "User update", "User delete"],
    ];

    public function actionIndex(int $truncate = 0)
    {
        $this->stdout("Database initialization.\n");
        $this->stdout("This process will insert base data into database.\n");
        if (!$this->confirm("Continue? ")) {
            return;
        }
        if ($truncate == 1) {
            $this->stdout("Truncating permissions.\n");
            $this->truncateTables(["auth_assignment", "auth_item", "auth_item_child", "auth_rule"]);
        }
        $this->stdout("Initializing authentication items...\n");
        $start = microtime(true);
        $auth = Yii::$app->getAuthManager();

        foreach ($this->roles as $role) {
            $this->addRole($auth, $role);
        }
        foreach ($this->permissions as $permission) {
            $this->addPermission($auth, $permission);
        }
        foreach ($this->relations as $parent => $children) {
            foreach ($children as $child) {
                $this->addChild($auth, $parent, $child);
            }
        }
        $elapsed_time = Yii::$app->formatter->asDecimal(microtime(true) - $start, 3);
        $this->stdout("Completed. (took {$elapsed_time} seconds)\n");
    }

    private function addRole(ManagerInterface $auth, string $roleName)
    {
        $this->stdout("Adding role '{$roleName}: ");
        $role = $auth->createRole($roleName);
        try {
            $auth->add($role);
            $this->authItems[$roleName] = $role;
            $this->stdout("done.\n");
        } catch (Exception $e) {
            $this->authItems[$roleName] = $auth->getRole($roleName);
            $this->stdout("skip.\n");
        }
    }

    private function addPermission(ManagerInterface $auth, string $permissionName)
    {
        $this->stdout("Adding permission '{$permissionName}: ");
        $permission = $auth->createPermission($permissionName);
        try {
            $auth->add($permission);
            $this->authItems[$permissionName] = $permission;
            $this->stdout("done.\n");
        } catch (Exception $e) {
            $this->authItems[$permissionName] = $auth->getPermission($permissionName);
            $this->stdout("skip.\n");
        }
    }

    private function addChild(ManagerInterface $auth, string $parent, string $child)
    {
        $this->stdout("Adding child '{$child}' to parent '{$parent}' ");
        $parent = $this->authItems[$parent];
        $child = $this->authItems[$child];
        try {
            $auth->addChild($parent, $child);
            $this->stdout("done.\n");
        } catch (Exception $e) {
            $this->stdout("skip.\n");
        }
    }

    /**
     * Truncates a list of tables without foreign keys checks.
     * @param array $tables
     */
    private function truncateTables(array $tables = [])
    {
        $db = Yii::$app->db;
        try {
            $db->createCommand("SET FOREIGN_KEY_CHECKS = 0;")->execute();
        } catch (\yii\db\Exception $e) {
            $this->stdout("Could not set FOREIGN_KEY_CHECKS to 0, truncating table may fail\n");
        }
        foreach ($tables as $table) {
            $this->stdout(str_pad("Truncating table {$table}: ", 50, ' ', STR_PAD_RIGHT));
            try {
                $db->createCommand()->truncateTable($table)->execute();
                $this->stdout("ok.\n");
            } catch (Exception $e) {
                $this->stdout("error.\n");
            }
        }
        try {
            $db->createCommand("SET FOREIGN_KEY_CHECKS = 1;")->execute();
        } catch (\yii\db\Exception $e) {
            $this->stdout("Could not set FOREIGN_KEY_CHECKS to 1\n");
        }
    }
}