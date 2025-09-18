<?php

namespace service\xplan;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Target extends \service\base\Module{
    
    
    public function createTarget($plan_entity, $creator_id, $schema_name, $visibility='private'){
        if (!array_key_exists('title', $plan_entity)) {
            return $this->buildResponse('error', 401, 'Plan title is required.');
        }
        
        $com_schema = $this->loadCom('schema');
        if (false==$com_schema->hasSchema($schema_name)) {
            return $this->buildResponse('error', 402, 'Plan schema is not exists.');
        }
        $schema = $com_schema->loadSchema($schema_name);
        
        $com_user = $this->loadCom('user');
        $creator = $com_user->loadUser($creator_id);
        if (empty($creator) || false===$com_user->hasPermission($creator, 'CreateTarget')){
            return $this->buildResponse('error', 403, 'The creator is invalid.');
        }
        
        if (false===$this->checkEntity($plan_entity)) {
            return $this->buildResponse('error', 404, $this->getErrors());
        }
        
        if (!in_array($visibility, array('public', 'protected', 'private'))) {
            return $this->buildResponse('error', 405, 'Visibility is invalid.');
        }
        
        $re = $this->_create($plan_entity, $creator_id, $schema, $visibility);
        if(false===$re) {
            return $this->buildResponse('error', 406, 'System error: failed to create target.');
        }
        
        return $this->buildResponse('success', 201, $re);
    }
    
    
    public function loadTarget($target_id){
        
    }
    
    
    public function loadTargets($user_id, $schema='', $options=[]){
        
    }
    
    public function search($query, $rows, $page){
        
    }
    
    
    protected function _create($plan_entity, $creator_id, $schema, $visibility){
        $mod_target = new models\Target();
        $mod_target->setAttributes($plan_entity);
        $mod_target->setAttribute('creator_id', $creator_id);
        $mod_target->setAttribute('schema', $schema['name']);
        
        $target_id = $mod_target->save();
        
        if(false===$target_id) {
            return false;
        }
        
        return $mod_target;
    }

    
    protected function checkEntity($target_entity){
        return true;
    }
    
}