<?php
/*
 * Created on Sep 16, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function frontend_preset_view_frontpage(&$vars, $path){
	$re = new stdClass();
    $re->section = 'frontpage';
    $re->data = array();
    
    return $re;
}
function frontend_preset_view_city(&$vars, $path){
	$preSetViews = array();
    $section = 'CityChannel';
    
    $re = new stdClass();
    $re->section = $section;
    
    $preSetCiews['global']['current-city'] = cache_get('current_city_term', null);
    
    $re->data['options'] = $preSetViews;
    
    return $re;
}

function frontend_preset_view_book(&$vars, $path){
    $preSetViews = array();
    $section = 'book';
    
    $reMatch = preg_match('/^book\/([0-9]+)$/', request_path(), $matched);
    
    $re = new stdClass();
    $re->section = $section;
    
    if (true == $reMatch) {
    	$re->data['global'] = array('bookId'=>$matched[1]);
    }
    
    return $re;
}

function frontend_preset_view_activity(&$vars, $path){
	
    preg_match_all('/^activity\/([a-z0-9A-Z]+)/', $path, $matched);
    
    $re = new stdClass();
    
    if (empty($matched)) {
    	return false;
    }
    
    $param = $matched[1][0];
    if (is_numeric($param)) {
        $re->section = 'activity/detail';
        $re->data['global'] = array('activityId'=>$param);
    }else{
        $re->section = 'activity/' . $param;
        $re->data = array();
    }
    
    return $re;
}