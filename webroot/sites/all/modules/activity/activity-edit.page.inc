<?php
/*
 * Created on 11 Dec, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function activity_edit($aid){
	global $user;
    
    $activity = new stdClass();
    
    require_once(drupal_get_path('module', 'node').'/node.pages.inc');
    
    if (!is_numeric($aid)) {
    	drupal_set_message('无效请求');
        return '';
    }
    $nodes = node_load_multiple(array($aid));
    if (empty($nodes)) {
    	drupal_set_message('活动不存在。');
        return '';
    }
    $activity = array_pop($nodes);
    
    if ('activity'!=$activity->type) {
    	drupal_set_message('无效请求');
        return '';
    }
    
    $form = node_page_edit($activity);
    
    unset($form['language']);
    unset($form['additional_settings']);
    return $form;
}