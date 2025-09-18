<?php
namespace service\xplan\coms;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use \service\xplan\models\Plan;


abstract class AbstractComponent extends \servic\base\Component{
    
    
    protected $basic_entity = null;
    
    
    public function setBasicEntity(Plan $basic_entity){
        $this->basic_entity = $basic_entity;
    }
}