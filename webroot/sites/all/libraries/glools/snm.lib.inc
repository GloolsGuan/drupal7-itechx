<?php
/*
 * Created on 2011-10-24
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

!defined(DRUPAL_ROOT) or exit("The library Lib_Glools_Mincs is running for drupal 7 system.");
module_exists('glools') or exit("Before you use Lib_Glools_Mincs you should enable glools module first.");

Lib_Glools_Load::loadFile('glools/base', 'lib');
Lib_Glools_Load::loadFile('glools/snm/form', 'lib');

//require_once(dirname(__FILE__).'/form.lib.inc');

class Lib_Glools_Snm extends Lib_Glools_Base{
    
    protected $features = array();
    
    public function __construct(&$form, &$form_state) {
        //Set default submit hook, The hook is drupal 7 default hook come from node.admin.inc
    	Lib_Glools_Snm_Form::$form = $form;
        Lib_Glools_Snm_Form::$form_state = $form_state;
        Lib_Glools_Snm_Form::$submitHook = array("node_filter_form_submit");
    }
    
    public function getForm() {
        
        foreach ($this->features as $objFeature) {
            $objFeature->beforeBuildFeature();
            $objFeature->buildFeatureElement();
            $objFeature->afterBuildFeature();
            
            $objFeature->AssignToForm();
        }
        
        if ($this->getMesCount('warning')>0 || $this->getMesCount('error')>0) {
            $this->assignMesToDrupal('warning');
            $this->assignMesToDrupal('error');
            return false;
        }
        
        return Lib_Glools_Snm_Form::$form;
    }
    
    public function setFormProperties(array $properties) {
    	if (empty($properties)) {
    		return false;
    	}
        
        foreach ($properties as $k=>$v) {
        	if ('#' !== $k{0}) {
        	    $this->setMessage('Provided Property is invalid for method '.__METHOD__.'.', 'warning');
                continue;
        	}
            Lib_Glools_Snm_Form::$form[$k] = $v;
        }
        return $this;
    }
    
    /**
     * $features = array(
     *     array('name'=>'Feature name', 'data'=>'the data assign to the Feature', 'options'=>array('special parameters for the Feature'))
     * )
     * */
    public function setFeatures(array $features) {
        if( empty($features) ) {
        	return false;
        }
                
        foreach( $features as $name=>$parameters ) {
            $objF = $this->loadFeature($name);
            
            if (false == $objF) {
            	$this->exception("Specified Feature does not existed.");
                break;
            }
            if (!empty($parameters['data'])) {
                $objF->setData($parameters['data']);
            }
           
            if (!empty($parameters['options'])) {
           	     $objF->setOptions($parameters['options']);
            }
           
            if (!empty($parameters['eventHooks'])) {
                $objF->registerEvents($parameters['eventHooks']);
            }
           
        	$this->features[$name] = $objF;
        }
    }
    
    public function registerFeatureEvents($feature, $events) {
    	
        if (!is_string($feature) || !isset($this->features[$feature])) {
    		$this->setMessage('Provided event hooks for feathure "'.$feature.'" are invalid.', 'warning');
            return false;
    	}
        
        $this->features[$feature]->registerEvents($events);
    }
    
    protected function loadFeature($name) {
        
        if (empty($name) || !is_string($name)) {
        	return false;
        }
    	
        if (false == Lib_Glools_load::loadFile('glools/snm/features/'.strtolower($name), 'lib')) {
        	$this->setMessage('Feature file '.$name.' does not exists for '.__METHOD__, 'warning');
            return false;
        }
        
        $class = 'Lib_Glools_Snm_Features_'.ucfirst($name);
        
        if (!class_exists($class)) {
        	return false;
        }
        
        return new $class();
    }
    
    
}