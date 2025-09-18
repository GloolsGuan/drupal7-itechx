<?php
namespace Ebouti\Business\Models;
/* 
 * Default Business Resource
 * 
 * 
 */

class Resource extends \Gtools\SmartModel\Model{
   
    
    
    public function loadSchema(){
        return include(__DIR__ . '/../includes/BusinessResource.schema');
    }
    
}