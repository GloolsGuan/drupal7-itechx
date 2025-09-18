<?php

/**
 * 
 * 
 */
function _frontend_preset_custom_activities_icons($view){
	/*
    $cityTerm = cache_get('current_city_term');
    
    if (empty($cityTerm->data)) {
    	return null;
    }
    $cityTermId = $cityTerm->data->tid;
    */
    
    if (!empty($view['tag'])) {
    	$hook = '_frontend_preset_custom_activities_icons_' . strtolower($view['tag']);
        if (function_exists($hook)) {
        	return $hook;
        }
    }
    
    $modelActivity = Lib_Glools_Load::load('activity', 'model');
    $activities = $modelActivity->loadPromotedActivities(null, null, 56);
    
    return node_view_multiple($activities, 'frontLatestActivity');
}