<?php
namespace api\common\models;

use yii\base\Model;

class Image extends Model
{
    const UPLOAD_PATH = '@media/';
    
    /**
     * @var null|\yii\web\UploadedFile
     */
    public $imageFile;
    
    /**
     * @var string Final URL to uploaded image
     */
    public $src;
    
    /**
     * @var string Extracted file name
     */
    public $file_name;
    
    /**
     * @var string Extracted mime type information
     */
    public $mime_type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                'imageFile', 'image',
                'skipOnEmpty' => false,
            ],
        ];
    }
    
    /**
     * Uploads the image to the specified directory inside '@media/'
     */
    public function upload(string $directory)
    {
        if ($this->validate()) {
            $upload_path = \Yii::getAlias(self::UPLOAD_PATH) . "{$directory}/";

            $this->file_name = $this->imageFile->name;
            $this->mime_type = $this->imageFile->type;
            $file = $upload_path . $this->file_name;
            
            \yii\helpers\BaseFileHelper::createDirectory($upload_path, 0775, true);
            
            $this->src = \Yii::$app->mediaUrlManager->createUrl("{$directory}/" . $this->file_name);
            return $this->imageFile->saveAs($file);
        }
    }
}
