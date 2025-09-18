<?php
namespace Gtools\SmartModel;
/* 
 * Resource scheme文档构建与解析服务
 * 
 */

class Schema extends \yii\Base\Model{
    
    public $name = '';
    
    public $brief = '';
    
    public $schema_type = '';
    
    public $fields = [];
    
    public $bundles = [];
    
    protected $defination = [];
    
    
    protected $group = [];
    
    
    public function __construct($schema){
        //\Gtools\Debug::testLog(__METHOD__, $schema, __LINE__);
        parent::__construct($schema);
        //\Gtools\Debug::testLog(__METHOD__, $this, __LINE__);
    }
    
    public function setResource($resource){
        //\Gtools\Debug::testLog(__METHOD__, $resource, __LINE__);
    }
    
    
    public function setFields($fields){
        //\Gtools\Debug::testLog(__METHOD__, $fields, __LINE__);
    }


    public function init(){
        if(empty($this->defination)){
            return ;
        }
        
        $this->checkingDefination();
    }
    
    public function getName(){
        return $this->name;
    }
    
    
    public function getFields(){
        \Gtools\Debug::testLog(__METHOD__, $this->fields, __LINE__);
        return $this->fields;
    }
    
    
    public function getBundles(){
        return $this->bundles;
    }
    
    
    
    /**
     * Get an value by xpath "a/b/c"
     * 
     * @param type $xpath
     */
    public function xpathValue($xpath){
        
    }
    
    
    
    
}

