<?php
/*
 * Created by Glools Guan on Aug 13, 2012
 * 
 * version:0.1 [20120813]
 * 
 * # Aug 13, 2012 # The class used for preprocessing view data. It is means that view class can load preprocess data
 * dynamicly.
 * 
 * Where you should predefine view preset hooks?
 * MODULE_NAME/inc_presets/[VIEW_NAME].preset.inc
 * [VIEW_NAME] parameter should be lower case.
 * You should create the "inc_presets" directory manually now.
 * 
 * What is the predefine function name rule for view preset?
 * _[MODULE NAME]_preset_[VIEW_NAME]
 * 
 * What is the viewName?
 * View name is not view class name but the view preset name, It is the same as "view name" in views-preset.data.inc
 * but you should replace '/' by '_'. 
 * */

!defined(DRUPAL_ROOT) or exit("The library Lib_Glools_Mincs is running for drupal 7 system.");
module_exists('glools') or exit("Before you use Lib_Glools_View_Preset you should enable glools module first.");

Lib_Glools_Load::loadFile('glools/base', 'lib');

class Lib_Glools_View_Preset extends Lib_Glools_Base{
	
    protected $view = null;
    
    protected $module = '';
    
    protected $viewName = '';
    
    public function __construct(){
    	
    }
    
    public function load($view, $module) {
        
        if (!($view instanceof Lib_Glools_View_Abstract)) {
        	$this->exception('Provided parameter for '.__CLASS__.' is invalid.');
        }
        
        if (empty($module) || !module_exists($module)) {
        	$this->exception('Provided module does not exists for '.__CLASS__.'::'.__METHOD__.'.');
        }
        
        $this->view = $view;
        $this->module = $module;
        $this->buildViewName();
        
        return $this->loadPreset();
        
    }
    /**
     * Load preset data.
     * */
    protected function loadPreset() {
        
        if (false==$this->loadPresetFile()) {
            return false;
        }
        
        $func = $this->buildPresetHook();
        
        if (!function_exists($func)) {
        	$this->exception('The view preset ['. $func .'] hook does not defined.');
            return false;
        }
        
        return $func($this->view);
    }
    
    protected function buildViewName(){
        if (null==$this->view) {
        	$this->exception('Provided parameter for '.__CLASS__.'::'.__METHOD__.' is invalid.');
        }
        
        $this->viewName = str_replace('/', '_', $this->view['view']);
        return true;
    }
    
    /**
     * Build preset hook by view and module parameters.
     * */
    protected function buildPresethook() {
    	
        return '_'.$this->module.'_preset_'.strtolower($this->viewName);
    }
    
    protected function loadPresetFile() {
        
    	$path = drupal_get_path('module', $this->module);
        $presetFile = $path.'/inc_presets/' . strtolower($this->viewName) .'.preset.inc';
        
        if (!file_exists($presetFile)) {
        	return false;
        }
        
        include_once($presetFile);
        return true;
    }
}