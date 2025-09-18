<?php

function frontend_channel_home(){
    return 'This is home channel.';
}

function frontend_channel_home_viewPreset(&$vars, $path){
    
    $re = new stdClass();
    if ('home'==request_path()) {
        $re->section = 'home';
        drupal_add_css(path_to_theme() . '/css/channels/home.channel.css');
    }else{
    	$re->section = 'home/tab';
    }
    
    $re->data = array();
    
    $re->data['options'] = array();
    
    return $re;
}