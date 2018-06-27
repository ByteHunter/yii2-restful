<?php
namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * User model
 *
 * @property string $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property ApiAccess $apiAccess
 */
class User
    extends ActiveRecord
{
    use \common\traits\AuthenticationModelTrait;
    use \common\traits\AfterSaveRefreshModelTrait;
    
    const STATUS_DELETED    = 'deleted';
    const STATUS_SUSPENDED  = 'suspended';
    const STATUS_ACTIVE     = 'active';

    const SCENARIO_CREATE = 'create';
    const SCENARIO_LOGIN = 'login';

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('now()'),
            ]
        ];
    }

    public function rules()
    {
        return [
            [
                ['email', 'password_hash'], 'required',
                'on' => [static::SCENARIO_DEFAULT]
            ],
            [
                ['email', 'password'], 'required',
                'on' => [static::SCENARIO_LOGIN]
            ],
            [
                ['email', 'password'], 'required',
                'on' => [static::SCENARIO_CREATE]
            ],
            [["email"], "email"],
            [['status'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['username', 'email', 'password_hash'], 'string', 'max' => 255],
            [
                ['username', 'email'],
                'unique', 'on' => [static::SCENARIO_DEFAULT, static::SCENARIO_CREATE]
            ],
            [
                'status', 'default',
                'value' => self::STATUS_ACTIVE
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

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert) {
            $this->setPassword($this->password);
        }
        return true;
    }

    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->refresh();
        if ($insert) {
            $this->createApiAccess();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function createApiAccess()
    {
        $apiAccess = new ApiAccess([
            'user_id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'type' => ApiAccess::TYPE_USER,
        ]);
        $apiAccess->save();
    }

    public function getApiAccess() : \yii\db\ActiveQuery
    {
        return $this->hasOne(ApiAccess::class, ['user_id' => 'id']);
    }

    public function isActive() : bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isDeleted() : bool
    {
        return $this->status === self::STATUS_DELETED;
    }

    public function isSuspended() : bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }
}
