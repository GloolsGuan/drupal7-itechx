<?php
namespace Ebouti\Boutique;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Base extends \Gtools\Yii\Module{
    
    static public function loadSubModule($name, $args=[]){
        $ns = __NAMESPACE__;
        
        $name = trim($name);
        if(false==preg_match('#[a-z]+#i', $name)){
            return false;
        }
        
        $sub_ns = sprintf("%s\%s", $ns, ucfirst($name));
        
        $ref = new \ReflectionClass($sub_ns);
        return $ref->newInstanceArgs($args);
    }
}
