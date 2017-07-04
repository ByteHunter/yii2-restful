<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * Admin model
 *
 * @property int $id
 * @property string $auth_key
 * @property string $password_reset_token
 * @property string $account_confirm_token
 * @property string $status
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $created_at
 * @property string $updated_at
 * 
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 */
class Admin extends ActiveRecord
{
    use \common\traits\AuthenticationModelTrait;
    use \common\traits\AfterSaveRefreshModelTrait;
    
    const STATUS_DELETED    = 'deleted';
    const STATUS_SUSPENDED  = 'suspended';
    const STATUS_ACTIVE     = 'active';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('now()'),
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['auth_key', 'username', 'email', 'password_hash', 'status'],
                'required'
            ],
            [
                ['status'], 'string'
            ],
            [
                ['created', 'updated', 'password'], 'safe'
            ],
            [
                ['auth_key'], 'string', 'max' => 32
            ],
            [
                ['username', 'email', 'password_reset_token', 'account_confirm_token'],
                'string', 'max' => 255
            ],
            // Unique attributes
            [
                ['password_reset_token', 'account_confirm_token', 'username', 'email'],
                'unique'
            ],
            // Describe `status` attribute
            [
                'status', 'default',
                'value' => self::STATUS_SUSPENDED
            ],
            [
                'status', 'in',
                'range' => [
                    self::STATUS_ACTIVE,
                    self::STATUS_DELETED,
                    self::STATUS_SUSPENDED
                ]
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'auth_key'              => 'Auth Key',
            'password_reset_token'  => 'Password Reset Token',
            'account_confirm_token' => 'Account Confirm Token',
            'status'                => 'Status',
            'username'              => 'Username',
            'email'                 => 'Email',
            'password_hash'         => 'Password Hash',
            'created_at'            => 'Created At',
            'updated_at'            => 'Updated At',
        ];
    }
    
    /* ---------------------------------------------------------------------------------------------
     * Relations
     * ------------------------------------------------------------------------------------------ */

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['admin_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('auth_assignment', ['admin_id' => 'id']);
    }
    
    /* ------------------------------------------------------------------------
     * Utilities
     * ------------------------------------------------------------------------ */

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->status === self::STATUS_DELETED;
    }
    
    /**
     * @return boolean
     */
    public function isSuspended()
    {
        return $this->status === self::STATUS_SUSPENDED;
    }
}
