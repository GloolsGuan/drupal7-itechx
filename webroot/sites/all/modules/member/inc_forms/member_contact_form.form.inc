<?php
/*
 * Created on Mar 7, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function member_contact_form($form, &$form_state){
	global $user;
    $args = func_get_args();
    
    $queried = db_query('select * from {users_contact} where uid=:uid limit 1', array(':uid'=>$user->uid));
    $retrieved = $queried->fetchAllAssoc('uid');
    
    $userContact = array();
    if (!empty($retrieved)) {
    	$userContact = array_pop($retrieved);
    }
    
    $form = array(
        '#type'           => 'form',
        '#weight'         => 0,
        '#theme_wrappers' => array('glools_form_wrapper'),
        '#title'          => t('Set your contact information for participating the activity'),
        '#description'    => t('Please be sure your contact information is valid.'),
        '#attributes'     => array('method'=>'POST', 'class'=>'member_contact_form'),
        '#validate'       => array('glools_form_validate'),
        '#submit'         => array('glools_form_submit')
    );
    $form['info'] = array(
        '#type' => 'fieldset',
        //'#title' => t('Contact Information')
    );
    
    $form['info']['realname'] = array(
        '#type'             => 'textfield',
        '#title'            => t('Real name'),
        '#weight'           => 0,
        '#required'         => 1,
        '#default_value'    => empty($userContact->real_name) ? '' : $userContact->real_name,
        '#element_validate' => array('element_validate_username'),
        '#maxlength'        => 100,
        '#ajax' => array(
            'event' => 'blur',
            'callback' => 'glools_form_element_validate_ajax_callback',
            'effect'   => 'fade'
        )
        
    );
    $form['info']['mobile'] = array(
        '#type'   => 'textfield',
        '#title'  => t('Mobile'),
        '#weight' => 1,
        '#required' => 1,
        '#default_value' => empty($userContact->mobile) ? '' : $userContact->mobile,
        '#element_validate' => array('element_validate_mobile'),
        '#ajax' => array(
            'event' => 'blur',
            'callback' => 'glools_form_element_validate_ajax_callback',
            'effect'   => 'fade'
        )
    );
    
    $form['info']['email'] = array(
        '#type' => 'textfield',
        '#title' => t('Email'),
        '#weight' => 2,
        '#required' => 1,
        '#default_value'    => empty($userContact->email) ? '' : $userContact->email,
        '#element_validate' => array('element_validate_email'),
        '#ajax' => array(
            'event' => 'blur',
            'callback' => 'glools_form_element_validate_ajax_callback',
            'effect'   => 'fade'
        )
    );
    
    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Save'),
        '#weight' => 100,
        
    );
    
    return $form;
}

function member_contact_form_validate($form, &$form_state){
	
}

function member_contact_form_submit($form, &$form_state){
	global $user;
    
    $values = $form_state['values'];
    $retrieved = db_query('select * from {users_contact} where uid=:uid', array(':uid'=>$user->uid))->fetchAllAssoc('uid');
    if (empty($retrieved)) {
    	$sql = 'insert into {users_contact} (`uid`,`real_name`,`mobile`, `email`) values(:uid, :real_name, :mobile, :email);';
    }else{
    	$sql = 'update {users_contact} set `real_name`=:real_name, `mobile`=:mobile, `email`=:email where uid=:uid';
    }
    
    $queried = db_query($sql, array(':real_name'=>$values['realname'], ':mobile'=>$values['mobile'], ':email'=>$values['email'], ':uid'=>$user->uid));
    if (1==$queried->rowCount()) {
    	drupal_set_message(t('You have updated your contact information.'));
    }else{
    	drupal_set_message(t('Failed to updated your contact information.'), 'error');
    }
    
    return true;
}