<?php
namespace service\xplan;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Member extends \service\base\Module{
    
    
    public function invits($member, $target_id){
        
    }
    
    
    public function join($member, $target_id){
        
    }
    
    
    public function loadMembers($target_id){
        
    }
    
    
    /**
     * Assign role for participent of plan
     * 
     */
    public function assignRole(){
        
    }
    
    
    public function loadRoles(){
        
    }
    
    
    public function createRole($target_id, $role_name, $role_desc, $role_parent_id){
        
    }
    
    
    public function attachPermissionToRole($role_name, $per_id){
        
    }
    
    
    public function removePermissionFromRole($role_name, $per_id){
        
    }
    
    
    public function removeRole($target_id, $role_name){
        
    }
    
    
    public function attachPermissions($target_id, $permissions){
        
    }
    
    
    public function removePermission($target_id, $per_id){
        
    }
}