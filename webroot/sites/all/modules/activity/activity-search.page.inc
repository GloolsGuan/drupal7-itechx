<?php
/*
 * Created on 29 Oct, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function activity_search(){
	require_once(dirname(__FILE__).'/inc_models/activity.model.inc');
    $nodeIds = activity_model_getActivities(4);
	
	if( empty($nodeIds) ){
		return 'There is no valid activity.';
	}
	
	drupal_add_css(path_to_theme() . '/css/page-searchActivity.css');
	$build = node_view_multiple($nodeIds, 'latestActivity');
	
	$build['pager'] = array(
        '#theme' => 'pager',
        '#weight' => 5
    );
    
    return $build;
}