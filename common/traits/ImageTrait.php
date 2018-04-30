<?php
namespace common\traits;

trait ImageTrait
{
    public function hasImage() : bool
    {
        return !empty($this->{$this->imageAttribute()});
    }
    
    public function getImageSrc() : string
    {
        if ($this->hasImage()) {
            return $this->{$this->imageAttribute()};
        } else {
            return \Yii::$app->mediaUrlManager->createUrl($this->getDemoPath(), true);
        }
    }
    
    public function getDemoPath() : string
    {
        return "demo/{$this->tableName()}.png";
    }

    public function imageAttribute() : string
    {
        return 'image';
    }
}
