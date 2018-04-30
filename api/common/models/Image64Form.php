<?php

namespace api\common\models;


use yii\base\Model;

/**
 * Processes a base64 encoded image and saves it to a file
 * @package api\common\models
 */
class Image64Form
    extends Model
{
    const UPLOAD_PATH = '@media/';


    public $content;

    public $debug;

    /**
     * @var string Final URL to uploaded image
     */
    public $src;

    /**
     * @var string Extracted file name
     */
    public $file_name = "image";

    /**
     * @var string Extracted file name
     */
    public $file_path;

    /**
     * @var string Extracted mime type information
     */
    public $file_format;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["content"], "required"],
            [["content"], "string"]
        ];
    }

    /**
     * Uploads the image to the specified directory inside '@media/'
     * @param string $directory
     * @return bool
     * @throws \yii\base\Exception
     */
    public function upload(string $directory, bool $multiple = false) : bool
    {
        if ($this->validate()) {
            $decoded = base64_decode($this->content);
            $this->content = null;

            $this->file_format = $this->getFormat($decoded);
            $upload_path = \Yii::getAlias(self::UPLOAD_PATH) . "{$directory}/";
            \yii\helpers\BaseFileHelper::createDirectory($upload_path, 0775, true);

            $this->file_path = $this->generateName($upload_path, $multiple);

            file_put_contents("{$upload_path}{$this->file_path}", $decoded);

            $this->src = \Yii::$app->mediaUrlManager->createUrl("{$directory}/" . $this->file_path);
            return true;
        }

        return false;
    }

    private function getFormat(string $data) : ?string
    {
        $file_info = finfo_open();
        $mime_type = finfo_buffer($file_info, $data, FILEINFO_MIME_TYPE);
        $format = explode("/", $mime_type)[1];
        finfo_close($file_info);
        return $format;
    }

    private function generateName(string $directory, bool $multiple = false) : string
    {
        $name = "{$this->file_name}.{$this->file_format}";
        if (!$multiple) {
            return $name;
        } else {
            $counter = 2;
            while (file_exists("{$directory}{$name}")) {
                $name = "{$this->file_name}_{$counter}.{$this->file_format}";
                $counter++;
            }
            return $name;
        }
    }
}
