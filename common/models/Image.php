<?php

namespace common\models;

/**
 * This is the model class for table "image".
 *
 * NOTE: This is a base implementation
 *
 * @property integer $id
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
            [["path"], "string"],
            ["type", "string", "max" => 64],
            ['type', 'in', 'range' => [
                self::TYPE_USER
            ]],
        ];
    }
}
