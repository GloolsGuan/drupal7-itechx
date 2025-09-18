<?php
/*
 * Created on Jan 16, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function frontend_channel_frontpage_info(){
    // Do not need to register any menu
}

function frontend_channel_frontpage(){
    if (file_exists( path_to_theme() . '/css/channels/frontpage.channel.css')) {
    	//drupal_add_css(path_to_theme() . '/css/channels/frontpage.channel.css');
        
        drupal_add_css(_theme_lasooo_importCSS(path_to_theme() . '/css/channels/frontpage.channel.css') , 'inline');
    }
    
    drupal_add_js(path_to_theme() . '/js/page-front.js');
}


/**
 * @Invoke by [frontend]/inc_libraries/funcs.lib.inc
 * 
 * 
 * 
 * @param type $vars
 * @param type $path
 * @return \stdClass
 */
function frontend_channel_frontpage_viewPreset(&$vars, $path){
    
    $re = new stdClass();
    $re->section = 'frontpage';
    $re->data = array();
    
    $re->data['options'] = array();
    
    return $re;
}
