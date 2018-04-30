<?php
namespace api\common\components\actions;

interface ResourceManyRelationInterface
{
    public function manyRelations();

    public function getManyRelationActions();

    public function getManyRelationAccessRule();

    public function getManyRelationActionVerbs();
}
