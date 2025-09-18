<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace service\field\field;
use service\field\field\inc\FieldAbstract;

class FieldNumericsModule extends FieldAbstract{
    
    public $default_value = array();
    
    public function beforeBuild() {
        
        //Lib_Gtools_Debug::testLog(__FILE__, array($this->label, $this->default_value), __METHOD__ . ':before');
        
        if (!empty($this->default_value) && is_array($this->default_value)) {
            
            $this->default_value = array(
                'min' => !empty($this->default_value['min']) ? $this->default_value['min'] : 0,
                'rule' => !empty($this->default_value['rule']) ? $this->default_value['rule'] : NULL,
                'max' => !empty($this->default_value['max']) ? $this->default_value['max'] : 0
            );
        } else {
            $this->default_value =  array(
                'min' => 0,
                'rule'=>null,
                'max'=>0
            );
        }
        
        //Lib_Gtools_Debug::testLog(__FILE__, $this->default_value, __METHOD__ . ':after');
    }
    
    public function buildForStorage($value){
        
        if (!is_array($value)) {
            return '';
        }
        
        
        return serialize($value);
    }
}
