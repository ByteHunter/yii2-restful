<?php
namespace api\modules\v1\models;

use yii\web\Linkable;
use yii\web\Link;
use yii\helpers\Url;

class User extends \common\models\User implements Linkable
{
    use \api\common\traits\DeleteExceptionTrait;
    
    public function fields()
    {
        return [
            'id',
            'username',
            'email',
        ];
    }
    
    public function getLinks()
    {
        return [
            Link::REL_SELF => urldecode(Url::to([
                'user/view',
                'id' => $this->id
            ], true))
        ];
    }
}

