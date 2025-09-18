<?php
/*
 * Created on 15 Nov, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

!defined(DRUPAL_ROOT) or exit("The library Lib_Glools_Mincs is running for drupal 7 system.");
module_exists('glools') or exit("Before you use Lib_Glools_Loadinc you should enable glools module first.");

Lib_Glools_Load::loadFile('glools/base', 'lib');

class Lib_Glools_Loadinc extends Lib_Glools_Base{
	
    protected $module = '';
    
    protected $name = '';
    
    protected $type = '';
    
    public function __construct($module){
    	$this->setModule($module);
    }
    
    public function setModule($module){
    	if( !is_string($module) || false == module_exists($module) ){
    		return false;
    	}
        
        $this->module = $module;
    }
    
    public function getModule(){
    	return $this->module;
    }
    
    public function load($name, $type){
    	
        $incFile = $this->getPath($name, $type);
        
        if( false == $incFile ){
        	return false;
        }
        
        return require_once($incFile);
    }
    
    public function loadTpl($name, $vars=array()){
    	$incFile = $this->getPath($name, 'tpl');
        
        if (false == $incFile) {
        	return false;
        }
        
        extract($vars, EXTR_SKIP);
         
        ob_start();
        require_once($incFile);
        return ob_get_clean();
    }
    
    public function getPath($name, $type){
    	if( empty($this->module) ){
            return false;
        }
        
        $typeInfo = $this->getTypeInfo($type);
        
        $file = drupal_get_path('module', $this->module).'/inc_'.$typeInfo['path_suffix'].'/'.$name.'.'.$typeInfo['file_suffix'].'.inc';
        
        if( file_exists($file) ){
        	return $file;
        }
        
        return false;
    }
    
    protected function getTypeInfo($type){
        static $types;
        if(!is_string($type)) {
            throw new Exception('Invalid parameters for Glools_Inc_Tools::getTypeInfo.');
        }
        
        if (empty($types)) {
            $types = self::defaultIncTypes();
        }
        
        if (isset($types[$type])) {
            return $types[$type];
        }
        //var_dump(debug_backtrace());
        //debug_print_backtrace();
        throw new Exception('Invalid parameters for Glools_Inc_Tools::getTypeInfo.');
        
        return false;
    }
    
    static protected function defaultIncTypes(){
        return array(
            'lib'      => array('path_suffix'=>'libraries', 'file_suffix'=>'lib'),
            'data'     => array('path_suffix'=>'data', 'file_suffix'=>'data'),
            'preset'   => array('path_suffix'=>'presets', 'file_suffix'=>'preset'),
            'channel'  => array('path_suffix'=>'channels', 'file_suffix'=>'channel'),
            'tpl'      => array('path_suffix'=>'templates', 'file_suffix'=>'tpl'),
            'form'     => array('path_suffix'=>'forms', 'file_suffix'=>'form'),
            'render'   => array('path_suffix'=>'renders', 'file_suffix'=>'render'),
            'widget'   => array('path_suffix'=>'widgets', 'file_suffix'=>'tw')
        );
    }
    
}