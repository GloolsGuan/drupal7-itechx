<?php
/*
 * Created on 11 Dec, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/**
 * Book activity
 * - show book activity page.
 * */
function activity_book($aid){
    global $user;
    
    $formItems = array();
    
    
    if (!user_access('book activity')) {
    	if (0==$user->uid) {
    		drupal_set_message(t('Before you participent a activity, you should login first.'));
    	}else{
    		drupal_set_message('You have no permission to book activity.', 'notice');
    	}
        
        return '';
    }
    
    if (true == activity_agree_participating($aid)) {
    	$status = _activity_book($aid);
        
        switch ($status) {
            case 0:
                drupal_set_message('Congratulation, you have book the activity successfully.');
                break;
            case -1:
                drupal_set_message(t('Failed to book the activity.'));
                break;
            case -2:
                drupal_set_message(t('You have booked the activity.'));
                break;
            default:
                drupal_set_message('Failed to book the activity.');
        }
    }else{
        
        //member_valid_contact($user);
        
        //$form['contact'] = drupal_get_form('member_contact_form');
        $form['agreement'] = drupal_get_form('activity_participating_agreement');
        
        /*
        $form['notice'] = array(
            '#theme' => 'div',
            '#weight' => 99,
            '#vars' => array(
                'attributes' => array('class'=>'notice'),
                'content'    => Glools_Inc_Tools::renderTpl(array(), 'participent-activity-notice', 'activity')
            )
        );
        */
    	return $form;
    }
    
    return '';
}

function activity_debook($aid){
	global $user;
    
    $queried = db_query('delete from {activity_participant} where user_id=:uid and instance_id=:iid', array(':uid'=>$user->uid, 'iid'=>$aid));
    if ($queried->rowCount()>0) {
    	drupal_set_message(t('You have debooked the activity.'));
    }else{
    	drupal_set_message(t('Failed to debook the activity.'));
    }
    return '';
}

function _activity_book ($aid) {
    global $user;
    
	if (!user_access('book activity')) {
		return false;
	}
    
    $data = array(
        'user_id' => $user->uid,
        'instance_id' => $aid,
        'status' => 'booked',
        'note' => 'Do not need to pay for the activity on line.'
    );
    
    $booked = db_query("select * from {activity_participant} where user_id=:uid and instance_id=:instance_id", array(':uid'=>$user->uid, ':instance_id'=>$aid))->fetchAssoc();
    
    if (false==$booked) {
    	$re = db_insert('activity_participant')->fields($data)->execute();
        if ($re) {
        	return 0;
        }else{
        	return -1;
            watchdog('system', '%type: !System error, Recording book activity to la_activity_participant table error. ', array('%type'=>'system error'), $severity = WATCHDOG_NOTICE, $link = NULL);
        }
    }
    
    return -2;
}

function activity_participating_form($aid){
    global $user;
    $form = array();
    
    if(!user_access('book activity') || 1>$user->uid) {
    	drupal_set_message('You should <a href="/user/login">@login</a> or <a href="/user/register">@register</a> before you book the activity.', 'notice');
        return false;
    }
    
    foreach(array(1,2,3,4,5,6) as $k){
        $f = '_activity_book_step_'.$k;
        $form[$k] = $f($aid);
    }
    return $form;
}

/**
 * Understand activity workflow of participatation.
 * */
function _activity_book_step_1($aid){
	$form = array();
    
    $form['agreement-fieldset'] = array(
        '#type' => 'fieldset',
        '#title' => t('活动协议'),
        '#collapsible' => TRUE,
        '#collapsed' => FALSE
    );
    $form['agreement-fieldset']['agreement'] = array(
        '#type'  => 'textarea',
        '#value' => 'hello,world'
    );
    $form['agreement-fieldset']['agree'] = array(
        '#type'  => 'checkboxes',
        '#options' => array('yes'=>t('同意'), 'no'=> t('不同意')),//drupal_map_assoc(array(t('同意'), t('不同意'))),
        '#default_value' =>array('no'),
        '#description' => 'After you agree "The activity participation agreement", ' .
                'It is means that you and lasooo site has build something.',
        '#required' => true
    );
    //$form['agreement-fieldset']
    return $form;
}

/**
 * activity participatation agreement
 * */
function _activity_book_step_2(){
    
}

/**
 * To know lasooo public welfare
 * */
function _activity_book_step_3(){
    
}

/**
 * Check your contact information
 * */
function _activity_book_step_4(){
    
}

/**
 * Prepay to lasooo for activity
 * */
function _activity_book_step_5(){
    
}

/**
 * Book activity successing notice
 * */
function _activity_book_step_6(){
    
} 