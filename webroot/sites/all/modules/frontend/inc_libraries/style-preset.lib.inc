<?php

/**
 * What kind of style you need to load it here.
 * 
 * 1. custom views
 * 2. 
 */

function frontend_preset_styles_blogs (&$vars, $path){
	//drupal_add_css(path_to_theme() . '/css/components/blog.com.css');
    
    // Add blog component style for blog content page.
    if (preg_match('/^blogs\/[0-9]+/', $path)) {
    	drupal_add_css(path_to_theme() . '/css/components/blog.com.css');
    }
    //debug_record_data(__FUNCTION__, array_keys($vars));
}


function frontend_preset_styles_activity(&$vars, $path){
    if ('activity/create'==$path || preg_match('/^activity\/[0-9]+\/edit/', $path)) {
    	drupal_add_css(path_to_theme() . '/css/components/form.com.css');
    }
}