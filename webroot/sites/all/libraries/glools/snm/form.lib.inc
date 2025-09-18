<?php
/*
 * Created on 2011-10-25
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
!defined(DRUPAL_ROOT) or exit("The library Lib_Glools_Mincs is running for drupal 7 system.");
module_exists('glools') or exit("Before you use Lib_Glools_Mincs you should enable glools module first.");

class Lib_Glools_Snm_Form{
	
    // Form element provided by drupal_get_form
    static $form = null;
    
    // Form state provided by drupal_get_form
    static $form_state = null;
    
    // Nodes original data retrieve from database
    static $nodes = null;
    
    // Feathures, Provided by SNM, what you want to added to the 
    static $features = array();
    
    // Submit Hooks, e.g array('node_filter_form_submit') or 'node_filter_form_submit'
    // As a array you can set more than one hook, as a string just one hook allowed.
    static $submitHook = null;
    
    // Form element for drupal_get_from
    static $formElement = null;
    
}