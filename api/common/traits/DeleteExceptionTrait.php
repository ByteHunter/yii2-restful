<?php 
namespace api\common\traits;

trait DeleteExceptionTrait
{
    /**
     * Catch integrity constraint violation showing instead a HTTP Error code.
     * {@inheritDoc}
     * @see \yii\db\ActiveRecord::delete()
     */
    public function delete()
    {
        if (parent::delete() === false) {
            throw new \yii\web\ForbiddenHttpException("This model cannot be deleted.");
        }
    }
}
