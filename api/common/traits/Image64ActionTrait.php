<?php
namespace api\common\traits;


trait Image64ActionTrait
{
    public function multipleImage()
    {
        return false;
    }

    public function getImage64Action()
    {
        return [
            "image" => [
                'class' => 'api\common\components\UploadImage64Action',
                'modelClass' => $this->modelClass,
                'multipleImage' => $this->multipleImage(),
                'checkAccess' => [$this, 'checkAccess'],
                'imageAttribute' => $this->modelClass::imageAttribute(),
            ],
        ];
    }
    
    public function getImage64AccessRule()
    {
        return [
            'allow' => true,
            'actions' => ['image'],
            'roles' => ['@'],
        ];
    }
    
    public function getImage64ActionVerbs()
    {
        return [
            "image" => ["POST"]
        ];
    }
}
