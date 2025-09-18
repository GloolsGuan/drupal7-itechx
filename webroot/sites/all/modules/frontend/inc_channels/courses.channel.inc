<?php

/**
 * Show I joined courses.
 * @return string
 */

function frontend_channel_courses(){
	return 'Hello,world! This is courses page, Welcome to glools.com.';
}


function frontend_channel_square_viewPreset(&$vars, $path){
    
    $re = new stdClass();
    $re->section = 'square';
    $re->data = array();
    
    $re->data['options'] = array();
    
    return $re;
}