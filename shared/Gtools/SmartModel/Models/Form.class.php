<?php
namespace Gtools\SmartModel\Models;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * -- Render node structure --
 * [field_name => [
 *  'wrapper' => true|[settings as an node],
 *  'tag' => ''
 * ]];
 * 
 */

class Form extends \yii\helpers\Html{
    
    public $method = '';
    
    //-- Resource model --
    public $model = null;
    
    public $process_url = '';
    
    public $nodes = [];
    
    public $group_first = false;
    
    public $groups = [];
    
    public $resource =  [];
    
    public $resource_name = '';
    
    public $resource_model = null;
    
    public function __construct($resource_name, $fields, $config=[]) {
        $this->resource_name = $resource_name;
        
        if(empty($fields)){
            throw new \Exception('Failed to build Form instance, Fields is required.');
        }
        
        foreach($fields as $name=>$field){
            if(!($field instanceof \Gtools\SmartModel\AbstractField)){
                throw new \Exceptio('Field must inherited from SmartModel\AbstractField.');
            }
            $this->setField($name, $field);
        }
        
        if(array_key_exists('fields', $config)){
            unset($config['fields']);
        }
        
        \Yii::configure($this, $config);
    }


    /**
     * Build form html code
     */
    public function build(){
        return sprintf('%s, %s', 'Hello,world!', __METHOD__);
    }
    
    
    /**
     * Build single node
     * 
     * @param type $tag_name
     * @param type $settings
     * @param type $content
     */
    public function buildNode($field_name, $content=''){
        $node = $this->loadNode($field_name);
        
        return self::tag($node['field_name'], $content, $node);
    }
    
    public function buildWrapper(){
        
    }
    
    
    public function setField($name, $field){
        $this->nodes[$name] = $field;
    }
    
    
    /**
     * @see yii/request
     */
    public function buildCsrfToken(){
        
    }
}