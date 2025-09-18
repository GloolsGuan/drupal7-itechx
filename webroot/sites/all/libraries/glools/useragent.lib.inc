<?php
/*
    Parse http user_agent string for detecting client device
    
    @Author: GloolsGuan@glools.com
    
**/

require_once(dirname(__FILE__).'/base.lib.inc');

class Lib_Glools_Useragent extends Lib_Glools_Base{
	
    protected $agentString;
    
    protected $devices = array();
    
    protected $deviceTypes = array();
    
    protected $clientInfo = array();
    
    public function __construct(){
        $this->agentString = $_SERVER['HTTP_USER_AGENT'];
        
        $this->deviceTypes = array('pc', 'unknown', 'mobile', 'pad');
        
        $this->clientInfo = array(
            // Device name, iphone, computer, ipad ... 
            //'device'      =>  '',
            // pc, unknown, mobile, pad
            //'d_type'      =>  '',
            //'system'      =>  '',
            //'explorer'    =>  '',
            //'e_version'   =>  ''
        );
    }
    
    public function getClientInfo(){
        //return $this->clientInfo;
        debug_rdata(__METHOD__, empty($this->clientInfo));
        if (empty($this->clientInfo)) {
        	$this->detectAgent();
        }
        debug_rdata(__METHOD__, $this->clientInfo);
        return $this->clientInfo;
    }
    
    protected function detectAgent(){
        $strAPosition = strpos($this->agentString, '(');
        $strBPosition = strpos($this->agentString, ')');
        $clientInfo = substr($this->agentString, $strAPosition+1, $strBPosition-$strAPosition-1);
        
        $clientInfoArgs = explode(';', $clientInfo, 2);
        
        preg_match('/[a-zA-Z0-9]+/', $clientInfoArgs[0], $matched);
        
        $detectHook = 'detectAgent_' . strtolower($matched[0]);
        
        if (method_exists($this, $detectHook)) {
            $this->$detectHook($clientInfo);
        }else{
            $this->detectAgent_default($clientInfo);
        }
    }
    
    protected function detectAgent_default($clientInfo){
        $clientInfo = array(
            'device'      =>  'computer',
            'd_type'      =>  'pc'
        );
        $this->setClientInfo($clientInfo);
    }
    
    protected function detectAgent_compatible($clientInfo){
        $clientInfo = array(
            'device'      =>  'computer',
            'd_type'      =>  'pc'
        );
        
        $this->setClientInfo($clientInfo);
    }
    
    protected function detectAgent_linux($clientInfo){
        $clientInfo = array();
        
        preg_match_all('/android|mobile/i', $this->agentString, $matched);
        
        $matched = array_map('strtolower', $matched[0]);
        
        if(in_array('mobile', $matched)) {
        	$clientInfo['d_type'] = 'mobile';
        }else{
        	$clientInfo['d_type'] = 'pad';
        }
        
        if (in_array('android', $matched)) {
        	$clientInfo['system'] = 'android';
            $clientInfo['device'] = 'Android Device';
        }
        
        //debug_rdata(__METHOD__, $clientInfo);
        $this->setClientInfo($clientInfo);
    }
    
    protected function detectAgent_iphone($clientInfo){
        $clientInfo = array(
            'device'      =>  'iphone',
            'd_type'      =>  'mobile'
        );
        
        //debug_rdata(__METHOD__, $clientInfo);
        $this->setClientInfo($clientInfo);
    }
    
    protected function detectAgent_ipad($clientInfo){
    	$clientInfo = array(
            'device'      =>  'ipad',
            'd_type'      =>  'pad'
        );
        
        //debug_rdata(__METHOD__, $clientInfo);
        $this->setClientInfo($clientInfo);
    }
    
    
    
    protected function setClientInfo($clientInfo){
        
        foreach($clientInfo as $k=>$v) {
        	$this->clientInfo[$k] = $v;
        }
        
    }
    
}