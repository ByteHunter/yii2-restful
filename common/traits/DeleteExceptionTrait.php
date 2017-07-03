<?php
namespace common\traits;

/**
 * This trait helps handling Database Integrity exception when deleting a model which 
 * has foreign dependencies.
 * 
 * @author Rostislav Pleshivtsev Oparina
 * @link bytehunter.net
 *
 */
trait DeleteExceptionTrait
{
    /**
     * Catch integrity constraint violation returning false instead.
     * {@inheritDoc}
     * @see \yii\db\ActiveRecord::delete()
     */
    public function delete()
    {
        try {
            parent::delete();
        } catch (\yii\db\IntegrityException $e) {
            return false;
            $this->addError(['Could not delete this item']);
        }
    }
}