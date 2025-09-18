<?php
namespace \Gtools\SmartModel\Coms\Fields\Category;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Gtools\SmartModel\Libs\AbstractField;

class Category extends AbstractField{
    
    public $tree = array();
    
    
    public function beforeBuild() {
        //Lib_Gtools_Debug::testLog(__FILE__, array($this->default_value, $this->values), __METHOD__);
    }
    
    public function buildForStorage($value) {
        
        return $value;
    }
    
    public function buildTree($tid, $element, $parent_id=0){
        
        
    }
}