<?php
/*
 * - Basic class -
 * 
 * Lib_Glools_Base is the basic class for "glools" library.
 * 
 * @Author: GloolsGuan@glools.com
 */
abstract class Lib_Glools_Base{
	
    protected $messages = array();
    protected $mesCount = array();
    
    function __set($name, $value){
    	if(isset($this->$name)){
    		$this->$name = $value;
    	}
    }
    
    function __get($name){
    	return $this->$name;
    }
    
    function exception($mes){
    	return new Exception($mes);
    }
    
    function setMessage($message, $type){
    	if(empty($type) || !in_array($type, array('normal', 'warning', 'notice', 'error'))){
    		$type = 'normal';
    	}
        if(!isset($this->messages[$type])){
        	$this->messages[$type] = array();
            $this->mesCount[$type] = 0;
        }
        $this->messages[$type][] = $type .' : '. $message;
        $this->mesCount[$type]++ ;
    }
    
    function getMesCount($type=null){
    	
        if(!empty($type) && isset($this->mesCount[$type])){
            return $this->mesCount[$type];
    	}elseif(!empty($type)){
    		return 0;
    	}
        
        $co = 0;
        foreach($this->mesCount as $ct){
        	$co += $ct;
        }
        return $co;
    }
    
    function getMessages($type = null){
        if(null != $type){
        	return $this->messages[$type];
        }
        
    	return $this->messages;
    }
    
    function clearMessages(){
    	$this->messages = array();
        $this->mesCount = array();
    }
    
    function assignMesToDrupal($type=null){
    	if(!empty($type) && isset($this->messages[$type])){
    		foreach($this->messages[$type] as $mes){
    			drupal_set_message($mes, $type);
    		}
    	}else{
            drupal_set_message("Provided type [$type] to ".__METHOD__.' is invalid.', 'warning');
    	}
    }
}
