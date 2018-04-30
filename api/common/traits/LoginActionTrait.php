<?php
namespace api\common\traits;

trait LoginActionTrait
{
    public function getLoginBehaviors()
    {
        return [
            'access' => [
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['?']
                    ]
                ]
            ],
            'authenticator' => [
                'except' => ["login"]
            ]
        ];
    }

    public function getLoginVerbs()
    {
        return ["login" => ["POST"]];
    }

    public function getLoginActions()
    {
        return [
            "login" => [
                'class' => \api\common\components\actions\LoginAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ];
    }
}
