<?php

use yii\db\Migration;

/**
 * Generated migration
 * Sets up authentication and authorization models
 *
 * @author Rostislav Pleshivtsev Oparina
 * @link bytehunter.net
 *
 */
class m170703_170000_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        /*
         * Table `auth_rule`
         */
        $this->createTable('auth_rule', [
            'name' => 'VARCHAR(64) NOT NULL ',
            'data' => 'TEXT NULL ',
            'created_at' => 'INT NULL ',
            'updated_at' => 'INT NULL ',
            
        ], $tableOptions);
        $this->addPrimaryKey('', 'auth_rule', ['name', ]);
        
        
        /*
         * Table `auth_item`
         */
        $this->createTable('auth_item', [
            'name' => 'VARCHAR(64) NOT NULL ',
            'type' => 'INT NOT NULL ',
            'description' => 'TEXT NULL ',
            'rule_name' => 'VARCHAR(64) NULL ',
            'data' => 'TEXT NULL ',
            'created_at' => 'INT NULL ',
            'updated_at' => 'INT NULL ',
            
        ], $tableOptions);
        $this->addPrimaryKey('', 'auth_item', ['name', ]);
        
        
        /*
         * Table `auth_item_child`
         */
        $this->createTable('auth_item_child', [
            'parent' => 'VARCHAR(64) NOT NULL ',
            'child' => 'VARCHAR(64) NOT NULL ',
            
        ], $tableOptions);
        $this->addPrimaryKey('', 'auth_item_child', ['parent', 'child', ]);
        
        
        /*
         * Table `admin`
         */
        $this->createTable('admin', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'auth_key' => 'VARCHAR(32) NOT NULL ',
            'password_reset_token' => 'VARCHAR(255) NULL ',
            'account_confirm_token' => 'VARCHAR(255) NULL ',
            'status' => 'ENUM("deleted", "suspended", "active") NOT NULL DEFAULT "active" ',
            'username' => 'VARCHAR(255) NOT NULL ',
            'email' => 'VARCHAR(255) NOT NULL ',
            'password_hash' => 'VARCHAR(255) NOT NULL ',
            'fullname' => 'VARCHAR(255) NULL ',
            'phone' => 'VARCHAR(32) NULL ',
            'created_at' => 'DATETIME NULL ',
            'updated_at' => 'DATETIME NULL ',
            'last_activity' => 'DATETIME NULL ',
            
        ], $tableOptions);
        
        
        /*
         * Table `auth_assignment`
         */
        $this->createTable('auth_assignment', [
            'item_name' => 'VARCHAR(64) NOT NULL ',
            'user_id' => 'INT UNSIGNED NOT NULL ',
            'created_at' => 'INT NULL ',
            
        ], $tableOptions);
        $this->addPrimaryKey('', 'auth_assignment', ['item_name', 'user_id', ]);
        
        
        /*
         * Table `api_access`
         */
        $this->createTable('api_access', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'username' => 'VARCHAR(255) NOT NULL ',
            'email' => 'VARCHAR(255) NOT NULL ',
            'password_hash' => 'VARCHAR(255) NOT NULL ',
            'access_token' => 'VARCHAR(32) NOT NULL ',
            'password_reset_token' => 'VARCHAR(255) NULL ',
            'type' => 'ENUM("admin", "client") NOT NULL DEFAULT "client" ',
            'status' => 'ENUM("deleted", "suspended", "active") NOT NULL DEFAULT "active" ',
            'last_activity' => 'DATETIME NULL ',
            'created_at' => 'DATETIME NULL ',
            'updated_at' => 'DATETIME NULL ',
            
        ], $tableOptions);
        
        
        /*
         * Table `user`
         */
        $this->createTable('user', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'auth_key' => 'VARCHAR(32) NOT NULL ',
            'password_reset_token' => 'VARCHAR(255) NULL ',
            'account_confirm_token' => 'VARCHAR(255) NULL ',
            'status' => 'ENUM("deleted", "suspended", "active") NOT NULL DEFAULT "active" ',
            'type' => 'ENUM("user", "player", "coach", "manager", "referee", "agent") NOT NULL DEFAULT "user" ',
            'licence' => 'VARCHAR(64) NULL ',
            'username' => 'VARCHAR(255) NOT NULL ',
            'email' => 'VARCHAR(255) NOT NULL ',
            'password_hash' => 'VARCHAR(255) NOT NULL ',
            'created_at' => 'DATETIME NULL ',
            'updated_at' => 'DATETIME NULL ',
            
        ], $tableOptions);
        
        
        /*
         * Indexes
         */
        $this->createIndex('fk_auth_item_auth_rule1_idx', 'auth_item', 'rule_name', false);
        $this->createIndex('fk_auth_item_child_auth_item1_idx', 'auth_item_child', 'child', false);
        $this->createIndex('fk_auth_assignment_admin1_idx', 'auth_assignment', 'user_id', false);
        
        /*
         * Foreign Keys
         */
        $this->addForeignKey('fk_auth_item_auth_rule1', 'auth_item', 'rule_name', 'auth_rule', 'name', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_auth_item_child_auth_item', 'auth_item_child', 'parent', 'auth_item', 'name', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_auth_item_child_auth_item1', 'auth_item_child', 'child', 'auth_item', 'name', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_auth_assignment_auth_item1', 'auth_assignment', 'item_name', 'auth_item', 'name', 'NO ACTION', 'NO ACTION');
        $this->addForeignKey('fk_auth_assignment_admin1', 'auth_assignment', 'user_id', 'admin', 'id', 'NO ACTION', 'NO ACTION');
        
    }
    
    public function down()
    {
        # Drop foreign keys
        $this->dropForeignKey('fk_auth_item_auth_rule1', 'auth_item');
        $this->dropForeignKey('fk_auth_item_child_auth_item', 'auth_item_child');
        $this->dropForeignKey('fk_auth_item_child_auth_item1', 'auth_item_child');
        $this->dropForeignKey('fk_auth_assignment_auth_item1', 'auth_assignment');
        $this->dropForeignKey('fk_auth_assignment_admin1', 'auth_assignment');
        
        
        # Drop tables
        $this->dropTable('auth_rule');
        $this->dropTable('auth_item');
        $this->dropTable('auth_item_child');
        $this->dropTable('admin');
        $this->dropTable('auth_assignment');
        $this->dropTable('api_access');
        $this->dropTable('user');
        
    }
}
