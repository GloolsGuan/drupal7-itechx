<?php
/*
 * Created on Aug 14, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function _frontend_preset_custom_activities_slide($viewName){
	$cityCache = cache_get('current_city_term');
    $cityTerm = $cityCache->data;
    
    $modelActivity = Lib_Glools_Load::load('activity', 'model');
    
    return $modelActivity->loadPromotedActivitiesInCity(5, $cityTerm, true);
}