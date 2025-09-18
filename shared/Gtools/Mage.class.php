<?php
namespace Gtools;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Mage{
    
    protected static $_object_manager = null;
    
    
    public static function getObjectManager(){
        if(null==self::$_object_manager)
            self::$_object_manager = \Magento\Framework\App\ObjectManager::getInstance();
        
        return self::$_object_manager;
    }
    
    
    /**
     * 
     * @param type $mage_ns
     */
    public static function createObject($mage_ns, $params=[]){
        return self::getObjectManager()->create($mage_ns, $params);
    }
}
