<?php
namespace api\common\traits;

trait ResourceRelationTrait
{
    public function getRelationActions(string $prefix = "sr-")
    {
        $actions = [];
        foreach ($this->relations() as $action => $relation) {
            $actions["{$prefix}{$action}"] = [
                'class' => 'api\common\components\SubResourceAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'relation' => $relation,
            ];
        }
        return $actions;
    }
    
    public function getRelationAccessRule(string $prefix = "sr-")
    {
        $actions = [];
        foreach (array_keys($this->relations()) as $action) {
            $actions[] = "{$prefix}{$action}";
        }
        return [
            'allow' => true,
            'actions' => $actions,
            'roles' => ['@'],
        ];
    }
    
    public function getRelationActionVerbs(string $prefix = "sr-")
    {
        $verbs = [];
        foreach (array_keys($this->relations()) as $action) {
            $verbs["{$prefix}{$action}"] = ["GET"];
        }
        return $verbs;
    }
}
