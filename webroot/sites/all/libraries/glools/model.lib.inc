<?php
/*
 * Created on 5 Nov, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

Lib_Glools_Load::loadFile('glools/base', 'lib');

abstract class Lib_Glools_Model extends Lib_Glools_Base
{	
    protected $tables = array();
    
    protected $db = null;
    
    // It is the same with "key" in DatabaseConnection or the first level key of $databases in settings.php
    protected $serverName = 'default'; 
    
    //// It is the same with "target" in DatabaseConnection or the second level key of $databases in settings.php
    protected $database = 'default';
    
    protected $dbOptions = array();
    
    protected $cacheTime = 1800;
    
    public function __construct() {
        
        if( null == $this->db ){
        	$this->db = Database::getConnection($this->serverName, $this->database);
        }
        
        $this->init();
	}

    public function init() {
    	
    }
    
    public function buildCacheKey($method, $args){
    	return 'lasooo.db.' . $method . '.' .md5(json_encode($args));
    }
    
    protected function setDefaultOptions(){
    	global $databases;
        
        if (!empty($this->serverName)) {
        	$this->dbOptions['target'] = $this->serverName;
        }
        
        if (!empty($this->database)) {
        	$this->dbOptions['key'] = $this->database;
        }
    }
    
    protected function setDBOptions($options){
    	if (!is_array($options) || empty($options['target'])) {
    		$this->dbOptions = array('target'=>'default');
    	}else{
    		$this->dbOptions = $options;
    	}
    }
    
    /**
     * Added by GloolsGuan@glools.com at 2012-08-24
     * Build table structure cache information on current database server.
     * */
    protected function buildTableCache(){
    	
    	$query = 'show tables';
    	$queried = $this->db->query($query);
    	$rows = $queried->fetchAllAssoc();
    	debug_record_data(__FUNCTION__, $rows);
    }
}