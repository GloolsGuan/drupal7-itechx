<?php
namespace Gtools\Base;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Config extends \Magento\Framework\DataObject{
 
    protected static $instance = [];
    
    static $_config = [];
    
    
    static public function get($key='', $index=null){
        $called_class = get_called_class();
        //\Gtools\Debug::testLog(__METHOD__, $called_class, __LINE__);
        if(!array_key_exists($called_class, self::$instance) || empty(self::$instance[$called_class])){
            self::$instance[$called_class] = new $called_class($called_class::$_config);
        }
        
        
        return self::$instance[$called_class]->getData($key, $index);
    }
    
    
}

