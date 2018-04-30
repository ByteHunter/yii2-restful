<?php
namespace common\models;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\db\Expression;

/**
 * This is the model class for table "api_access".
 *
 * @property string $id
 * @property integer $admin_id
 * @property integer $user_id
 * @property integer $company_id
 * @property integer $local_government_id
 * @property string $type
 * @property string $status
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $access_token
 * @property string $password_reset_token
 * @property string $last_activity
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 * 
 */
class ApiAccess
    extends ActiveRecord
    implements IdentityInterface
{
    const TYPE_USER   = 'user';

    const STATUS_DELETED    = 'deleted';
    const STATUS_SUSPENDED  = 'suspended';
    const STATUS_ACTIVE     = 'active';

    public static function tableName()
    {
        return 'api_access';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('now()'),
            ]
        ];
    }

    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['type', 'status'], 'string'],
            [['last_activity', 'created_at', 'updated_at'], 'safe'],
            [['username', 'email', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
            [['access_token'], 'string'],
            [['access_token'], 'unique'],
            ['type', 'default', 'value' => self::TYPE_USER],
            ['type', 'in', 'range' => [
                self::TYPE_USER
            ]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [
                self::STATUS_ACTIVE, self::STATUS_DELETED, self::STATUS_SUSPENDED
            ]],
        ];
    }

    public function getUser() : \yii\db\ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            static::setRole($this->id, $this->type);
            $this->generateToken();
            try {
                $this->update();
            } catch (\Exception $e) {}
        }
        $this->refresh();
    }
    
    /* ---------------------------------------------------------------------------------------------
     * Identity methods
     * ------------------------------------------------------------------------------------------ */

    public function isTokenValid() : bool
    {
        // TODO: add more verifications
        return $this->access_token !== null;
    }

    public function generateToken() : void
    {
        $signer = new Sha256();
        $builder = new Builder();
        $builder->setIssuer("https://api.plantgo.net")
            ->setAudience("https://api.plantgo.net")
            ->setIssuedAt(time())
            ->setNotBefore(time() + 60)
            ->setExpiration(time() + 7200);

        if ($this->isUser()) {
            $builder->set("user_id", $this->user_id);
            $builder->set("username", $this->username);
        }

        $builder->sign($signer, \Yii::$app->params['jwt.key']);
        $token = $builder->getToken();
        $this->access_token = (string)$token;
    }

    public static function setRole(int $id, string $roleName) : void
    {
        $auth = \Yii::$app->getAuthManager();
        $role = $auth->getRole($roleName);
        try {
            $auth->assign($role, $id);
        } catch (\Exception $e) {}
    }

    public static function findIdentity($id)
    {
        return static::findOne([
            'id' => $id,
            'status' => static::STATUS_ACTIVE
        ]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne([
            'access_token' => $token,
            'status' => static::STATUS_ACTIVE,
        ]);
    }

    public static function findByPasswordResetToken(string $token) : ?ApiAccess
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => static::STATUS_ACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid(string $token) : bool
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        throw new NotSupportedException('"getAuthKey" is disabled.');
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function validatePassword(string $password) : bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword(string $password) : void
    {
        try {
            $this->password_hash = Yii::$app->security->generatePasswordHash($password);
        } catch (\Exception $e) {}
    }
    
    /* ------------------------------------------------------------------------
     * Utilities
     * ------------------------------------------------------------------------ */

    public function isUser() : bool
    {
        return $this->type === self::TYPE_USER;
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
