<?php
/*
 * Created on 2011-10-26
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
Lib_Glools_load::loadFile('glools/snm/features/abstract', 'lib');

class Lib_Glools_Snm_Features_Pager extends Lib_Glools_Snm_Features_Abstract {
    
    protected $featureElements = array();
    protected $featureName = 'pager';
    protected $defaultNodeOperations = array();
    
    // Used for setting special hook name for building operation list. 
    protected $operationHooks = '';
    
    public function buildFeatureElement() {
    	
    	drupal_add_css(drupal_get_path('module', 'glools').'/inc_css/pager.block.css');
    	
        $this->featureElements = array(
	        '#theme' => 'pager',
	        '#weight' => 5
	    );
    }
}