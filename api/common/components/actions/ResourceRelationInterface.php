<?php
namespace api\common\components\actions;

interface ResourceRelationInterface
{
    public function relations();
    
    public function getRelationActions();
    
    public function getRelationAccessRule();
    
    public function getRelationActionVerbs();
}
