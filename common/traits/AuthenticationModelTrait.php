<?php
namespace common\traits;
use Yii;

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
     * Auxiliar password attribute which is used to calculate the hash.
     * @var string un-hashed password
     */
    public $password;
    
    /**
     * Prevent validation errors for `password_hash` and `user_id` parameters when creating
     * a new record, they has yet to be generated and it is done in `beforeSave()` event.
     * {@inheritDoc}
     * @see \yii\base\Model::afterValidate()
     */
    public function afterValidate()
    {
        if ($this->hasErrors('password_hash') &&
            $this->isNewRecord)
        {
            $this->clearErrors('password_hash');
        }
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
     */
    public function setPassword($password)
    {
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }
}