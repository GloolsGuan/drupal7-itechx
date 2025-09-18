<?php
/*
 * Created on 2011-10-26
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

abstract class Lib_Glools_Snm_features_Abstract extends Lib_Glools_Base{
	
    protected $data = null;
    
    protected $featureName = '';
    
    protected $eventHooks = array();
    
    protected $featureElements = null;
    
    protected $allowedEventTypes = array();
    
    public function __construct(){
    	
        $this->allowedEventTypes = array(
            'beforeBuildFeature',
            'afterBuildFeature'
        );
        $this->init();
        
    }
    
    public function init(){}
    
    public function assignToForm(){
    	if( empty($this->featureName) ){
    		$this->exception('feature name is empty in class '. __CLASS__.' .');
    	}
        Lib_Glools_Snm_Form::$form[$this->featureName] = $this->featureElements;
    }
    
    final public function registerEvent($type, $hook){
    	if( !$this->validEventType($type) || !function_exists($hook) ){
    		$this->setMessage("Please check your event type and event hook, just 'beforeBuildfeature' and 'afterBuildfeature' " .
                    "types were supported. and the event hook must be a functhion and can be invoke in ".__METHOD__);
            return false;
    	}
        
        if(!isset($this->eventHooks[$type])){
        	$this->eventHooks[$type] = array();
        }
        
        $this->eventHooks[$type][] = $hook;
    }
    
    final public function registerEvents($events){
    	if( empty($events) || !is_array($events) ){
    		$this->setMessage('Invalid events set for feathure' . __CLASS__.'.', 'warning');
    	}
        foreach( $events as $type=>$hooks ){
        	if( !$this->validEventType($type) ){
        		continue;
        	}
            foreach($hooks as $hook){
                $this->registerEvent($type, $hook);
            }
        }
    }
    
    final public function beforeBuildFeature(){
        $this->runEventHook('beforeBuildFeature');
    }
    
    final public function afterBuildFeature(){
        $this->runEventHook('afterBuildFeature');
    }
    
    protected function validEventType($type){
    	if( in_array($type, $this->allowedEventTypes) ){
    		return true;
    	}
        return false;
    }
    
    final public function runEventHook($type){
    	if(!is_string($type)){
    		return false;
    	}
        if( !empty($this->eventHooks[$type]) ){
    		foreach( $this->eventHooks[$type] as $hook ){
    			
                if( function_exists($hook) ){
    				$hook($this->featureElements);
                    /*
                    if( !empty($re) && false!= $re ){
                    	$this->featureElements += $re; 
                    }
                    */
    			}else{
    				debug_record_data('features-abstract.lib.inc:runEventHook', $hook. 'does not exist.');
    			}
    		}
    	}
        return $this;
    }
    
    
    abstract public function buildfeatureElement();
    
    public function setData($data=null){
    	if(null != $data){
    		$this->data = $data;
    	}
        return $this;
    }
    
    public function setOptions(array $options){
    	if(empty($options)){
    		return false;
    	}
        
        foreach($options as $k=>$o){
        	if(isset($this->$k)){
        		$this->$k = $o; 
        	}
        }
        return $this;
    }
}