<?php
namespace Ebouti\Business\Libs\Abstracts;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class BizComponent extends \yii\base\Component{
    
    
    public $model = null;
    
    public $resource_name = '';
    
    public function __construct($config = array()) {
        
        //-- Try to build structure --
        
        parent::__construct($config);
    }
    
    
    /**
     * @Return array, component introduction
     * -- Structure --
     * [
     *     'title' => '',
     *     'brief' => '',
     *     'name' => '',
     * ]
     */
    abstract public function info();
    
    /**
     * @see \Gtools\SmartModel\Schema defination.
     */
    abstract public function schema();
    
    abstract public function getBizType();
    
    public function getName(){
        $class = get_class($this);
        //\Gtools\Debug::testLog(__METHOD__, substr($class, strrpos($class, '\\')), __LINE__);
        return strtolower(substr($class, strrpos($class, '\\')+1));
    }
    
    public function load($biz_id){
        $model = $this->loadModel();
        return $model->loadResource($biz_id, $this->getBizType());
    }
    
    
    public function loadSchema(){
        $schema = $this->schema();
        //\Gtools\Debug::testLog(__METHOD__, $schema, __LINE__);
        return $schema;
    }
    
    /**
     * Build bundle model
     * 
     * @param Resource $resource
     * 
     * @return object \Gtools\SmartModel\Models\Bundle
     */
    
    public function buildBundle(){
        $schema = $this->schema();
        if(empty($schema)){
            throw new \Exception(sprintf('Failed to build bundle of "%s", There is no scheme defination.', get_class($this)));
        }
        
        $bundle_name = substr(get_class($this), strrpos('\\', get_class($this)));
        
        //-- \Gtools\SmartModel\Module::buildBundle();
        
        $bundle = \Yii::createObject('\Gtools\SmartModel\Models\Bundle', [
            'schema' => $schema,
            'name' => $bundle_name,
            'location' => __NAMESPACE__
        ]);
        
        if(!empty($bundle->getErrors())){
            $errors = implode("\\n", $bundle->getErrors());
            \Gtools\Debug::sysLog(__METHOD__, $errors);
            throw new \Exception(sprintf("Failed to build bundle, Schema and settings is invalid with the following errors: \n.", $errors));
        }
        
        return $bundle;
    }
    
    
    
    public function loadModel(){
        
        if(!empty($this->model)){
            return $this->model;
        }
        
        $class_instance = new \ReflectionClass(get_class($this));
        $model_ns = sprintf('%s\Model', $class_instance->getNamespaceName());
        //\Gtools\Debug::testLog(__METHOD__, [get_class($this), $class_instance->getNamespaceName(), $model_ns], __LINE__);
        
        
        $this->model = new $model_ns([
            'schema'=>$this->schema()
        ]);
        
        return $this->model;
    }
    
}