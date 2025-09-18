<?php
/*
 * Created on 29 Oct, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
/**
 * Activity establish step:
 * 1. read the documents about activity
 * */
function activity_create(){
	global $user, $language;
    
    $activity = new stdClass();
    
    require_once(drupal_get_path('module', 'node').'/node.pages.inc');
    
    $form = node_add('activity');
    
    //----------------------------------------------------
    unset($form['language']);
    unset($form['additional_settings']);
    //----------------------------------------------------
    
    return $form;
}
