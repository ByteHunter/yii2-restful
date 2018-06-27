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
            'name' => 'VARCHAR(64) NOT NULL',
            'data' => 'BLOB',
            'created_at' => 'INT NULL',
            'updated_at' => 'INT NULL',
            'primary key (name)',
        ], $tableOptions);
        
        
        /*
         * Table `auth_item`
         */
        $this->createTable('auth_item', [
            'name' => 'VARCHAR(64) NOT NULL',
            'type' => 'SMALLINT(6) NOT NULL',
            'description' => 'TEXT NULL',
            'rule_name' => 'VARCHAR(64) NULL',
            'data' => 'BLOB',
            'created_at' => 'INT NULL',
            'updated_at' => 'INT NULL',
            'primary key (name)',
            'index (type)',
            'foreign key (rule_name) references `auth_rule` (name) on delete set null on update cascade',
        ], $tableOptions);
        
        
        /*
         * Table `auth_item_child`
         */
        $this->createTable('auth_item_child', [
            'parent' => 'VARCHAR(64) NOT NULL ',
            'child' => 'VARCHAR(64) NOT NULL ',
            'primary key (parent, child)',
            'foreign key (parent) references `auth_item` (name) on delete cascade on update cascade',
            'foreign key (child) references `auth_item` (name) on delete cascade on update cascade',
        ], $tableOptions);

        /*
         * Table `user`
         */
        $this->createTable('user', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
            'status' => 'ENUM("deleted", "suspended", "active") NOT NULL DEFAULT "active"',
            'username' => 'VARCHAR(255) NULL',
            'email' => 'VARCHAR(255) NOT NULL',
            'password_hash' => 'VARCHAR(255) NOT NULL',
            'created_at' => 'DATETIME NULL',
            'updated_at' => 'DATETIME NULL',
            'primary key (id)',
        ], $tableOptions);
        
        /*
         * Table `api_access`
         */
        $this->createTable('api_access', [
            'id' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
            'user_id' => 'INT UNSIGNED NULL',
            'type' => 'ENUM("user") NOT NULL DEFAULT "user"',
            'status' => 'ENUM("deleted", "suspended", "active") NOT NULL DEFAULT "active"',
            'username' => 'VARCHAR(255) NULL',
            'email' => 'VARCHAR(255) NULL',
            'password_hash' => 'VARCHAR(255) NULL',
            'access_token' => 'TEXT NOT NULL',
            'password_reset_token' => 'VARCHAR(255) NULL',
            'account_confirm_token' => 'VARCHAR(255) NULL',
            'last_activity' => 'DATETIME NULL',
            'created_at' => 'DATETIME NULL',
            'updated_at' => 'DATETIME NULL',
            'primary key (id)',
            'foreign key (user_id) references `user` (id) on delete cascade on update cascade',
        ], $tableOptions);

        /*
         * Table `auth_assignment`
         */
        $this->createTable('auth_assignment', [
            'item_name' => 'VARCHAR(64) NOT NULL ',
            'user_id' => 'INT UNSIGNED NOT NULL ',
            'created_at' => 'INT NULL',
            'primary key (item_name, user_id)',
            'foreign key (item_name) references `auth_item` (name) on delete cascade on update cascade',
            'foreign key (user_id) references `api_access` (id) on delete cascade on update cascade',
        ], $tableOptions);

        /*
         * Table `image`
         */
        $this->createTable('image', [
            'id' => 'int unsigned not null auto_increment',
            'type' => 'varchar(64) not null',
            'path' => 'text not null',
            'primary key (id)'
        ], $tableOptions);
    }
    
    public function down()
    {
        # Drop tables
        $this->dropTable('auth_assignment');
        $this->dropTable('auth_item_child');
        $this->dropTable('auth_item');
        $this->dropTable('auth_rule');
        $this->dropTable('api_access');
        $this->dropTable('user');
        $this->dropTable('image');
    }
}
