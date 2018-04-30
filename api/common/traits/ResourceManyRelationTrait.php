<?php
namespace api\common\traits;

trait ResourceManyRelationTrait
{
    public function getManyActionName(string $resource, string $action, string $prefix = "sr-") : string
    {
        return "{$prefix}{$resource}-{$action}";
    }

    public function getManyRelationActions(string $prefix = "sr-")
    {
        $actions = [];
        foreach ($this->manyRelations() as $action => $relation) {
            $actions["{$prefix}{$action}"] = [
                'class' => 'api\common\components\SubResourceAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'relation' => $relation,
            ];
            $actions["{$prefix}{$action}-update"] = [
                'class' => 'api\common\components\UpdateSubResourceRelationAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'relation' => $relation,
            ];
            $actions["{$prefix}{$action}-add"] = [
                'class' => 'api\common\components\AddSubResourceRelationAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'relation' => $relation,
            ];
            $actions["{$prefix}{$action}-delete"] = [
                'class' => 'api\common\components\DeleteSubResourceRelationAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'relation' => $relation,
            ];
        }
        return $actions;
    }
    
    public function getManyRelationAccessRule(string $prefix = "sr-")
    {
        $actions = [];
        foreach (array_keys($this->manyRelations()) as $action) {
            $actions[] = "{$prefix}{$action}";
            $actions[] = "{$prefix}{$action}-update";
            $actions[] = "{$prefix}{$action}-add";
            $actions[] = "{$prefix}{$action}-delete";
        }
        unset($actions["sc-index"]);
        return [
            "access" => [
                "rules" => [
                    [
                        'allow' => true,
                        'actions' => ["{$prefix}index"],
                        'roles' => ["?", "@"],
                    ],
                    [
                        'allow' => true,
                        'actions' => $actions,
                        'roles' => ['@'],
                    ]
                ]
            ]
        ];
    }
    
    public function getManyRelationActionVerbs(string $prefix = "sr-")
    {
        $verbs = [];
        foreach (array_keys($this->manyRelations()) as $action) {
            $verbs["{$prefix}{$action}"] = ["GET"];
            $verbs["{$prefix}{$action}-update"] = ["PATCH"];
            $verbs["{$prefix}{$action}-add"] = ["POST"];
            $verbs["{$prefix}{$action}-delete"] = ["DELETE"];
        }
        return $verbs;
    }
}
