<?php
namespace \Gtools\SmartModel\Coms\Fields\Datetime;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Gtools\SmartModel\Libs\AbstractField;

class Datetime extends AbstractField{
    
    
    public function beforeBuild() {
        if (!empty($this->default_value) && '0000-00-00 00:00:00'==$this->default_value) {
            $this->default_value = '';
        }
    }
}