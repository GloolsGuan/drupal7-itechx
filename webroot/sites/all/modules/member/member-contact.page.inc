<?php


function member_contact_edit(){
	global $user;
    //debug_record_data(__FUNCTION__, $user);
    
    if (1>$user->uid) {
    	return t('You have no right to visit the page, Please login in to lasooo site before you edit your contact information.');
    }
    
    
    return drupal_get_form('member_contact_form');
}