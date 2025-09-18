<?php
/*
 * Created on Sep 28, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function activity_getActivityInfo($userId){
	
    db_query('select * from {activity} where creator=:userId', array(':userId'=>$userId));
    
}


function activity_savePeriod($activityNode){
	
}

function activity_form_activity_node_form_alter(&$form, &$form_state, $form_id){
	global $user;
    
    if (!user_is_logged_in($user) || !is_activity_owner($user)) {
    	return false;
    }
    
    $activityInfo = activity_getActivityInfo($user->uid);
    /* 
    $form['activity_period'] = array(
        '#type' =>'textfield',
        '#value' => t('The No.1'),
        '#title' => t('Activity Period')
    );
    */
}

function is_activity_owner($user){
	return true;
}