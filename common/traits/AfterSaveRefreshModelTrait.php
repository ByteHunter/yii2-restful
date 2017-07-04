<?php
namespace common\traits;
use Yii;


/**
 * Adds an auto refresh after save to the model
 *
 * @author Rostislav Pleshivtsev Oparina
 * @link bytehunter.net
 *
 */
trait AfterSaveRefreshModelTrait
{
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
}
