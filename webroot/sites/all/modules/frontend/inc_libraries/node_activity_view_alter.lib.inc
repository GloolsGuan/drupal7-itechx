<?php
/*
 * Created on Sep 14, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function frontend_node_activity_view_alter(&$node){
	
    $requestPath = request_path();
    //var_dump($requestPath); 
    if (preg_match_all('/^city\/([\x{4e00}-\x{9fa5}]+)/u', $requestPath, $matched)) {
    	frontend_node_activity_view_alter_forCityChannel($node, $matched[1]);
    }
    
}

function frontend_node_activity_view_alter_forCityChannel(&$node, $city){
    #To do: add participent and follower information
    
}
