<?php

namespace api\common\components;

class UrlRule extends \yii\rest\UrlRule
{
    public $relations = [];

    public $crudRelations = [];

    public $paged = true;

    public $prefix = 'sr-';

    public function init()
    {
        $this->pluralize = false;
        $this->extraPatterns =
            $this->extraPatterns +
            $this->pagedPatterns() +
            $this->relationPatterns() +
            $this->crudRelationPatterns();
        parent::init();
    }

    public function pagedPatterns()
    {
        if (!$this->paged) {
            return [];
        }
        return [
            'GET per-page/<per-page>/page/<page>' => 'index',
            'GET page/<page>' => 'index',
            'GET <keys:keys>' => 'index',
        ];
    }

    public function relationPatterns()
    {
        $patterns = [];
        foreach ($this->relations as $relation) {
            $patterns["GET {id}/{$relation}"] = "{$this->prefix}{$relation}";
        }
        return $patterns;
    }

    public function crudRelationPatterns()
    {
        $patterns = [];
        foreach ($this->crudRelations as $relation)
        {
            $patterns["GET {id}/{$relation}"] = "{$this->prefix}{$relation}";
            $patterns["PATCH {id}/{$relation}"] = "{$this->prefix}{$relation}-update";
            $patterns["POST {id}/{$relation}"] = "{$this->prefix}{$relation}-add";
            $patterns["DELETE {id}/{$relation}/<subId>"] = "{$this->prefix}{$relation}-delete";
            $patterns["DELETE {id}/{$relation}"] = "{$this->prefix}{$relation}-delete";
            $patterns["OPTIONS {id}/{$relation}"] = "options";
            $patterns["OPTIONS {id}/{$relation}/<subId>"] = "options";
        }
        return $patterns;
    }
}
