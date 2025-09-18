<?php
namespace service\xplan;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Plan extends Target{
    
    
    
    public function create($plan_entity, $creator_id, $schema, $visibility='private'){
        return $this->createTarget($plan_entity, $creator, $schema, $visibility);
    }
    
    public function loadPlan($plan_id){
        return $this->loadTarget($plan_id);
    }
    
    
    public function loadPlans($user_id, $schema='', $options=[]){
        return $this->loadTargets($user_id, $schema, $options);
    }
    
    public function search($query, $rows, $page){
        return parent::search($query, $rows, $page);
    }
    
    
    /**
     * Both user_code and user_id supported
     * 
     * @param type $organizer_id
     */
    public function loadOrganizer($organizer_id){
       $organizer = new \stdClass();
       $organizer->title = '';
       
       return null;
    }
}