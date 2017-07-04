<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\db\Expression;

/**
 * This is the model class for table "api_access".
 *
 * @property string $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $access_token
 * @property string $password_reset_token
 * @property string $type
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $last_activity
 * 
 */
class ApiAccess extends ActiveRecord implements IdentityInterface
{
    const TYPE_ADMIN    = 'admin';
    const TYPE_CLIENT   = 'client';
    const STATUS_DELETED    = 'deleted';
    const STATUS_SUSPENDED  = 'suspended';
    const STATUS_ACTIVE     = 'active';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_access';
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
            [['access_token', 'username', 'email', 'password_hash'], 'required'],
            [['type', 'status'], 'string'],
            [['created', 'updated', 'last_activity'], 'safe'],
            [['access_token'], 'string', 'max' => 32],
            [['username', 'email', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            // Unique fields
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['access_token'], 'unique'],
            // Enum type fields
            ['type', 'default', 'value' => self::TYPE_CLIENT],
            ['type', 'in', 'range' => [
                self::TYPE_ADMIN, self::TYPE_CLIENT
            ]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [
                self::STATUS_ACTIVE, self::STATUS_DELETED, self::STATUS_SUSPENDED
            ]],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                    => 'ID',
            'username'              => 'Username',
            'email'                 => 'Email',
            'password_hash'         => 'Password Hash',
            'access_token'          => 'Access Token',
            'password_reset_token'  => 'Password Reset Token',
            'type'                  => 'Type',
            'status'                => 'Status',
            'created'               => 'Created',
            'updated'               => 'Updated',
            'last_activity'         => 'Last Activity',
        ];
    }
    
    /**
     * @return string[]
     */
    public function typeLabels()
    {
        return [
            self::TYPE_ADMIN    => 'admin',
            self::TYPE_CLIENT   => 'client'
        ];
    }
    
    /**
     * @return string[]
     */
    public function statusLabels()
    {
        return [
            self::STATUS_DELETED    => 'deleted',
            self::STATUS_SUSPENDED  => 'suspended',
            self::STATUS_ACTIVE     => 'active',
        ];
    }
    
    /* ---------------------------------------------------------------------------------------------
     * ActiveQuery calls
     * ------------------------------------------------------------------------------------------ */
    
    /**
     * @inheritdoc
     * @return ApiAccessQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ApiAccessQuery(get_called_class());
    }
    
    /* ---------------------------------------------------------------------------------------------
     * Events
     * ------------------------------------------------------------------------------------------ */
    
    /**
     * Automaticaly generate access token on save
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->access_token = Yii::$app->getSecurity()->generateRandomString();
        }
        return parent::beforeValidate();
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
        return static::findOne([
            'access_token' => $token,
            'status' => static::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by password reset token
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
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
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
        throw new NotSupportedException('"getAuthKey" is disabled.');
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return false;
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
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
    
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
    
    /* ------------------------------------------------------------------------
     * Utilities
     * ------------------------------------------------------------------------ */
    
    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->type === self::TYPE_ADMIN;
    }
    
    /**
     * @return boolean
     */
    public function isClient()
    {
        return $this->type === self::TYPE_CLIENT;
    }

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

/**
 * This is the ActiveQuery class for [[ApiAccess]].
 *
 * @see ApiAccess
 */
class ApiAccessQuery extends \yii\db\ActiveQuery
{
    /**
     * @return ApiAccessQuery
     */
    public function admin()
    {
        return $this->andWhere(['type' => ApiAccess::TYPE_ADMIN]);
    }
    /**
     * @return ApiAccessQuery
     */
    public function client()
    {
        return $this->andWhere(['type' => ApiAccess::TYPE_CLIENT]);
    }
    /**
     * @return ApiAccessQuery
     */
    public function deleted()
    {
        return $this->andWhere(['status' => ApiAccess::STATUS_DELETED]);
    }
    /**
     * @return ApiAccessQuery
     */
    public function suspended()
    {
        return $this->andWhere(['status' => ApiAccess::STATUS_SUSPENDED]);
    }
    /**
     * @return ApiAccessQuery
     */
    public function active()
    {
        return $this->andWhere(['status' => ApiAccess::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     * @return ApiAccess[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ApiAccess|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
