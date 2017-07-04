<?php
namespace console\controllers;


use yii\console\Controller;

class DbController extends Controller
{
    public function actionInit()
    {
        $start = microtime(true);
        $this->stdout("Database initialization.\n");
        $this->stdout("This process will insert base data into database.\n");
        if (!$this->confirm("Continue? ")) {
            return 0;
        }
        
        $this->stdout("Initializing authentication items... ");
        $auth = \Yii::$app->getAuthManager();
        $role = $auth->createRole("admin");
        try {
            $auth->add($role);
            $this->stdout("done.\n");
        } catch (\Exception $e) {
            $this->stdout("skip.\n");
        }
        
        $elapsed_time = \Yii::$app->formatter->asDecimal(microtime(true) - $start, 3);
        $this->stdout("Completed. (took {$elapsed_time} seconds)\n");
    }
    
    /**
     * Truncates all tables
     * @return number
     */
    public function actionClearAll()
    {
        if (!$this->confirm("Are you sure you want to clear whole database?")) {
            return 0;
        }
        $this->truncateTables([
            'admin', 'user',
        ]);
    }
    
    /**
     * Truncates a list of tables without foreign keys checks.
     * @param array $tables
     */
    private function truncateTables($tables = [])
    {
        $db = \Yii::$app->db;
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 0;")->execute();
        foreach ($tables as $table) {
            $this->stdout(str_pad("Truncating table {$table}: ", 50, ' ', STR_PAD_RIGHT));
            try {
                $db->createCommand()->truncateTable($table)->execute();
                $this->stdout("ok.\n");
            } catch (\Exception $e) {
                $this->stdout("error.\n");
            }
    
        }
        $db->createCommand("SET FOREIGN_KEY_CHECKS = 1;")->execute();
    }
}
