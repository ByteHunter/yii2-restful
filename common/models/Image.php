<?php

namespace common\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "image".
 *
 * NOTE: This is just an example of implementation
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $path
 */
class Image extends \yii\db\ActiveRecord
{
    const TYPE_USER = "user";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["type"], "required"],
            [["user_id"], "integer"],
            [["path"], "string"],
            ['type', 'in', 'range' => [
                self::TYPE_USER
            ]],
        ];
    }

    public function getUser() : ActiveQuery
    {
        return $this->hasOne(User::className(), ['id' => 'species_id']);
    }
}
