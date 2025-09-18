<?php
/*
 * Created on Feb 29, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function member_edit($type) {
    global $user;
	
    $hook = '_member_edit_' . $type;
    if (function_exists($hook)) {
    	return $hook($user);
    }
    
    drupal_add_message('System error, Please edit member information later.', 'notice');
    return null;
}


function _member_edit_general($user) {
	drupal_add_css(path_to_theme() . '/css/pages/member-edit-general.page.css');
    
    require_once(drupal_get_path('module', 'user') . '/user.pages.inc');
    
    return drupal_get_form('user_profile_form', $user);
}

function _member_edit_contact($user) {
    
    drupal_add_css(path_to_theme() . '/css/pages/member-edit-contact.page.css');
	
    return drupal_get_form('member_edit_contact_form', $user);
}

function member_edit_contact_form($form, &$form_state, $extra) {
	global $user;
    
    $contact = db_query('select * from {users_contact} where uid=:uid', array(':uid'=>$user->uid))->fetchAssoc();
    
    $form['title'] = array(
        '#children' => t('Edit user contact')
    );
    $form['real_name'] = array(
        '#type' => 'textfield',
        '#title' => t('Real name'),
        '#description' => t('The name should be the same as your ID card.'),
        '#required' => true,
        '#default_value' => empty($contact['real_name']) ? '' : $contact['real_name'] 
    );
    $form['idcard'] = array(
        '#type' => 'textfield',
        '#title' => t('ID card'),
        '#default_value' => empty($contact['idcard']) ? '' : $contact['idcard'],
        '#description' => t('The ID card number is your private data, It will used for encoding your lasooo card number.')
    );
    
    $form['mobile'] = array(
        '#type' => 'textfield',
        '#title' => t('Mobile'),
        '#required' => true,
        '#default_value' => empty($contact['mobile']) ? '' : $contact['mobile'],
        '#description' => t('Important imformation.')
    );
    
    $form['safe_email'] = array(
        '#type' => 'textfield',
        '#title' => t('Safe email'),
        '#required' => true,
        '#default_value' => empty($contact['safe_email']) ? '' : $contact['safe_email'],
        '#description' => t('The safe email used for fetching your password or reset your password, receiving important lasooo notice and so on.')
    );
    
    $form['submit'] = array(
        '#type' => 'submit',
        '#title' => 'Submit',
        '#value' => 'Submit'
    );
    
    
    
    return $form;
}

function member_edit_contact_form_validate(&$form, &$form_state) {
	//debug_record_data(__FUNCTION__, $form_state);
    
    $pregValidate = array(
        'real_name' => array('preg'=>'/^([a-zA-Z\.]+)|([\x{4e00}-\x{9fa5}]+)$/u', 'message'=>'The real name should be the same with your ID card or passport, Only english, "." and chinese character are supported.'),
        'idcard' => array('preg'=>'/^[0-9]{12,15}[a-z]{0,2}$/', 'message'=>'IDCARD support numeric character with 1-2 english character as suffix.'),
        'mobile' => array('preg'=>'/^[0-9]{11}$/', 'message'=>'The mobile should be number and must be 11 charcters.'),
        'safe_email' => array('preg'=>'/^[0-9a-zA-Z\.]+\@[a-z0-9\-]+\.[a-z]{2,3}$/', 'message'=>'Email should be valid address for receiving and checking your account.')
    );
    debug_record_data(__FUNCTION__, $form_state['values']);
    foreach ($pregValidate as $field=>$validate) {
    	if (!preg_match($validate['preg'], $form_state['values'][$field], $matched)) {
    		form_set_error($field, $validate['message']);
            
    	}
    }
    /*
    $errors = form_get_errors();
    if (empty($errors)) {
    	$form_state['#validated'] = true;
    }
    */
}

function member_edit_contact_form_submit(&$form, &$form_state) {
	global $user;
    
    $fields = array();
    foreach (array('real_name', 'idcard', 'mobile', 'safe_email') as $field) {
    	if (!empty($form_state['values'][$field])) {
    		$fields[$field] = $form_state['values'][$field];
    	}
    }
    
    $fields['uid'] = $user->uid;
    $fields['varified_item'] = implode(';', array_keys($fields));
    
    $userContact = db_query("select * from {users_contact} where uid=:uid limit 1", array(':uid'=>$user->uid))->fetchAssoc();
    
    if (false==$userContact) {
    	$re = db_insert('users_contact')->fields($fields)->execute();
    }else{
    	$re = db_update('users_contact', $fields)->fields($fields)->condition('uid', $user->uid)->execute();
    }
    debug_record_data(__FUNCTION__, $re);
    $form_state['redirect'] = 'member';
}