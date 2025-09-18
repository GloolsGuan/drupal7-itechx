<?php
/*
 * Created on Jun 20, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function member_user_login_alter(&$form, &$form_state){
	$form['actions']['forgetpassword'] = array(
        '#children' => l(t('Forget password'), 'user/password')
    );
    
}