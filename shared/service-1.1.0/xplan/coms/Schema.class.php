<?php
namespace service\xplan\coms;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Schema extends AbstractComponent{
    
    public function __construct($parent, $config){
        parent::__construct(__CLASS__, $parent, $config);
    }
    
    public function create(){
        
    }
    
    public function remove(){
        
    }
    
    public function update(){
        
    }
    
    public function loadSchema($schema_name){
        return [
            'name' => $schema_name
        ];
    }
    
    public function hasSchema($schema_name){
        
    }
}