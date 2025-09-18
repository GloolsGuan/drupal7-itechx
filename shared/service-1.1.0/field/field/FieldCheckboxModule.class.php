<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\field\field;
use service\field\field\inc\FieldAbstract;

class FieldCheckboxModule extends FieldAbstract{
    
    public function beforeBuild() {
        //Lib_Gtools_Debug::testLog(__FILE__, array($this->field_name, $this->label, $this->default_value), __METHOD__);
        if (!empty($this->default_value) && !is_array($this->default_value)) {
            $this->default_value = explode(',', $this->default_value);
        }
        //Lib_Gtools_Debug::testLog(__FILE__, $this->default_value, __METHOD__);
    }
    
    public function buildForStorage($value){
        
        if (!is_array($value)) {
            return $value;
        }
        return implode(',', $value);
    }
}