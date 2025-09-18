<?php
/*
 * Created on Mar 22, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function _frontend_preset_custom_members_avatar($view){
	
    switch ($view['private_key']) {
		case 'activity/owner':
            return _frontend_preset_custom_members_avatar_activityOwner();
            break;
        case 'home/private':
            return _frontend_preset_custom_members_avatar_private();
        default :
            ;
	}
}

function _frontend_preset_custom_members_avatar_activityOwner(){
	if (false===preg_match('/^activity\/([0-9]+)/', request_path(), $matched)){
        return null;
	}
    $node = node_load($matched[1]);
    if (!empty($node)) {
    	return user_load($node->uid);
    }
    
    return null;
    
}

function _frontend_preset_custom_members_avatar_private(){
	global $user;
    
    if ($user->uid>0) {
    	return $user;
    }
}