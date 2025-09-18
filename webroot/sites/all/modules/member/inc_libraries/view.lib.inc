<?php
/*
 * Created on Feb 29, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function _member_load_views($section, &$vars, $options=array()) {
    
    $objView = Lib_Glools_Load::load('glools/view', 'lib', array('module'=>'member'));
    
    $objView->loadPreset()->buildViews($section, $options);
    
    $objView->attachToPage($vars);
}