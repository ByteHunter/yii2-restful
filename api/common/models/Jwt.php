<?php

namespace api\common\models;

use common\models\ApiAccess;
use yii\base\Model;

/**
 * Class Jwt
 * @package api\common\models
 *
 * @property string $token
 */
class Jwt
    extends Model
{
    public $token;

    public function rules()
    {
        return [
            [["token"], "required"],
            [["token"], "string"],
        ];
    }

    public function verify()
    {
        $apiAccess = ApiAccess::findOne(["access_token" => $this->token]);
        return $apiAccess !== null;
    }
}
