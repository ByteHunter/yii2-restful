<?php
namespace api\common\traits;

trait ImageActionTrait
{
    public function getImageAction()
    {
        return [
            "image" => [
                'class' => 'api\common\components\UploadImageAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'imageAttribute' => $this->modelClass::imageAttribute(),
            ],
        ];
    }
    
    public function getImageAccessRule()
    {
        return [
            'allow' => true,
            'actions' => ['image'],
            'roles' => ['@'],
        ];
    }
    
    public function getImageActionVerbs()
    {
        return [
            "image" => ["POST"]
        ];
    }
}
