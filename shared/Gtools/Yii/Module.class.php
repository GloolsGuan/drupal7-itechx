<?php
namespace Gtools\Yii;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Module extends \yii\base\Module{
    
    
    protected static $coms = [];
    
    public function __construct($config=[]) {
        $called_class = get_called_class();
        parent::__construct($called_class, null, $config);
    }
    
    
    
    protected function events(){
        return [
            
        ];
    }
    

    public function loadComponent($ns, $args=[]){
        if(!array_key_exists($ns, self::$coms)){
            self::$coms[$ns] = \Yii::createObject($ns, $args);
        }
        
        return self::$coms[$ns];
    }
    

    protected function getMagentoObjectManager(){
        return \Magento\Framework\App\ObjectManager::getInstance();
    }
    
    
    public function buildResponse($code, $status, $data){
        return [
            'code'=>$code,
            'status' => $status,
            'data' => $data
        ];
    }
    
    
    public function getBasePath(){
        $module_instance = new \ReflectionClass($this);
        return dirname($module_instance->getFileName());
    }
    
    
    public function getName(){
        $module_instance = new \ReflectionClass($this);
        //\Gtools\Debug::testLog(__METHOD__, [$module_instance, $module_instance->getShortName()], __LINE__);
        $ns = $module_instance->getNamespaceName();
        $module_name = substr($ns, strrpos($ns, '\\')+1);
        //\Gtools\Debug::testLog(__METHOD__, $module_name, __LINE__);
        return $module_name;
    }
    
    
    
    
    public function buildJsonResponse($code, $status, $data){
        return json_encode($this->buildResponse($code, $status, $data));
    }
}