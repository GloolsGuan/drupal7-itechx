<?php
/*
 * Created on May 27, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
         
function element_validate_username(&$element, &$form_state, $complete){
    $re = user_validate_name($element['#value']);
    if (!empty($re)) {
    	glools_form_error($element, $re);
    }
    
    return true;
}

function element_validate_mobile($element, &$form_state){
    $mobile = $element['#value'];
    if (!preg_match('/^[0-9]{11}$/', $mobile)) {
    	glools_form_error($element, t('Invalid mobile number, It is must be eleven numbers.'));
    }
    
    return true;
}

function element_validate_email($element, &$form_state){
    //valid_email_address();
    $email = $element['#value'];
    
    if (!valid_email_address($email)) {
    	glools_form_error($element, t('Invalid email address.'));
    }
    return true;
}