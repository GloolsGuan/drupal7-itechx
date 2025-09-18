<?php
/*
 * Created on 13 Nov, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function member_load_member_photo($m) {
    if (!is_object($m) || !isset($m->uid)) {
    	return null;
    }
    
	$title = t("@user's picture", array('@user' => format_username($m)));
    
    if (is_object($m->picture)){
    	$userPicPath = $m->picture;
    } else {
        $userPicPath = empty($m->picture)? NULL : file_load($m->picture);
    }
    
    
    
    if (empty($userPicPath) || empty($userPicPath->uri)) {
        $userPicPath = new stdClass();
    	$userPicPath->uri = 'public://pictures/picture-default.jpg';
    }
    return theme('image', array('path' =>$userPicPath->uri , 'alt' => format_username($m), 'title' => $title));
}

function member_valid_contact($user) {
	if (empty($user) || !isset($user->uid)) {
		return false;
	}
    
    $contact = db_query("select * from {users_contact} where uid=:uid", array(':uid'=>$user->uid))->fetchAssoc();
    if (false==$contact) {
    	return false;
    }
    
    $varified = explode(';', $contact['varified_item']);
    $needed = array('real_name', 'idcard', 'mobile', 'safe_email');
    $v = true;
    
    foreach ($needed as $k) {
    	if (!in_array($k, $varified)) {
    		$v = false;
    	}
    }
    
    return $v;
}

function member_user_entity_info_extra($userEntity){
	
    $userEntity['bundles']['user_points'] = array(
        'label' => t('User Points'),
          'admin' => array(
            'path' => 'admin/config/people/accounts/extra',
            'access arguments' => array('administer users'),
          ),
    );
    
    return $userEntity;
}

function member_access($permission){
    
    $allowed = user_access($permission);
    
    if (false==$allowed) {
        drupal_goto('page/sys/denied/'.base64_encode(request_path(0)));
        return false;
    }
    return true;
}

/**
 * Checking wether the user is a valid member?
 * 1. The user should pass to the mobile checking.
 * 2. The user should have a valid email. 
 * */
function member_ismember(){
    ;
}

function member_load_coin($uid){
	
}

function member_load_point($uid){
	
}

function member_load_level($uid){
	
}

function member_load_defaultLevels($uid){
	
}