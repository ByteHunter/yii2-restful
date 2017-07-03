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
 * @property string $adminname
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
     * After saving refresh model to get rid of 'Expression Now()` y datetime fields
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::afterSave()
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $this->refresh();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['auth_key', 'adminname', 'email', 'password_hash', 'status'],
                'required'
            ],
            [
                ['status'], 'string'
            ],
            [
                ['created', 'updated'], 'safe'
            ],
            [
                ['auth_key'], 'string', 'max' => 32
            ],
            [
                ['adminname', 'email', 'password_reset_token', 'account_confirm_token'],
                'string', 'max' => 255
            ],
            // Unique attributes
            [
                ['password_reset_token', 'account_confirm_token', 'adminname', 'email'],
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
            'adminname'              => 'Username',
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
    
    /* ---------------------------------------------------------------------------------------------
     * Identity methods
     * ------------------------------------------------------------------------------------------ */

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id,
            'status' => static::STATUS_ACTIVE
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not supported.');
    }

    /**
     * Finds admin by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => static::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['admin.passwordResetTokenExpire'] ?? 1800;
        return $timestamp + $expire >= time();
    }
    
    /**
     * Finds admin by account confirmation token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByAccountConfirmToken($token)
    {
        return static::findOne([
            'account_confirm_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     * Generates new account confirmation token
     */
    public function generateAccountConfirmToken()
    {
        $this->account_confirm_token = Yii::$app->security->generateRandomString();
    }
    
    /**
     * Removes account confirmation token
     */
    public function removeAccountConfirmToken()
    {
        $this->account_confirm_token = null;
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
