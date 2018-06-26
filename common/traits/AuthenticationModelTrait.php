<?php
namespace common\traits;
use Yii;
use yii\base\NotSupportedException;

/**
 * Common authentication methods for user models
 * 
 * @author Rostislav Pleshivtsev Oparina
 * @link bytehunter.net
 *
 */
trait AuthenticationModelTrait
{
    /**
     * Auxiliary password attribute which is used to calculate the hash.
     * @var string un-hashed password
     */
    public $password;
    
    /**
     * {@inheritDoc}
     * @see \yii\base\Model::beforeValidate()
     */
    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->generateAuthKey();
            $this->status = self::STATUS_ACTIVE;
            $this->setPassword($this->password);
        }
        return parent::beforeValidate();
    }
    
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }
    
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }
    
    /**
     * @inheritdoc
     */
    public static function findByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findByAccessToken" is not supported.');
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
     * Finds user by account confirmation token
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
    public function getAuthKey()
    {
        return null; //return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return true; //return $this->getAuthKey() === $authKey;
    }
    
    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        //$this->auth_key = Yii::$app->security->generateRandomString();
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
}
