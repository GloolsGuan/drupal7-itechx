<?php
/*
 * Created on Feb 29, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function member_center() {
    global $user;
    
    $pathArgs = explode('/', request_path());
    
    
    if ('0' == $user->uid || '0'==$user->status) {
    	drupal_set_message(t('You need login in to visit member`s profile.'), 'notice');
        return null;
    }
    
    if (2==count($pathArgs)) {
    	$section = 'profile';
        $userName = $pathArgs[1];
        $memberBase = user_load_by_name($userName);
        if (false==$memberBase) {
        	$section = 'homepage';
        }else{
            drupal_add_css(path_to_theme() . '/css/pages/member-info.page.css');
            $memberModel = Lib_Glools_Load::load('member', 'model');
            $member = $memberModel->loadMember($memberBase->uid);
            $member['user'] = $memberBase;
        }
        
    }else{
    	$section = 'homepage';
    }
    
    
    switch($section) {
    	case 'homepage' :
            return t('Member homepage is developing...');
            break;
        case 'profile':
            return t('Member homepage is developing...');
            break;
        default:
            ;
    }
    
}

function member_center_info() {
	return 'member_center_info';
}

function member_center_contact() {
	return 'member_center_contact';
}

function member_center_account() {
	return 'member_center_account';
}