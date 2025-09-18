<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace service\field\field;
use service\field\field\inc\FieldAbstract;


class FieldDatetimeModule extends FieldAbstract{
    
    
    public function beforeBuild() {
        if (!empty($this->default_value) && '0000-00-00 00:00:00'==$this->default_value) {
            $this->default_value = '';
        }
    }
}