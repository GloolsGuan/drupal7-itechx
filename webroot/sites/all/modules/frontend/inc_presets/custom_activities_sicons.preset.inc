<?php

/**
 * 
 * 
 */
function _frontend_preset_custom_activities_sicons($view){
	/*
    $cityTerm = cache_get('current_city_term');
    
    if (empty($cityTerm->data)) {
    	return null;
    }
    $cityTermId = $cityTerm->data->tid;
    */
    
    $modelActivity = Lib_Glools_Load::load('activity', 'model');
    $activities = $modelActivity->loadPromotedActivities(null, null, 4, true);
    
    $buildNode = node_view_multiple($activities, 'frontLatestActivity');
    
    return array(
        'title' => t('优质活动推荐'),
        'nodes' => $buildNode
    );
}