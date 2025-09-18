<?php
namespace service\xplan;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Meet extends \service\base\Module{
    
    public function create(){
        
    }
    
    
    public function loadMeet(){
        
    }
    
    
    public function remove(){
        
    }
    
    
    public function addParticipent(){
        
    }
    
    
    public function removeParticipent(){
        
    }
    
    
    public function loadCom($com_name, $meet, $config=[]){
        $com_class = sprintf('meet\%s', ucfirst($com_name));
        return $com_class($meet, $config);
    }
}