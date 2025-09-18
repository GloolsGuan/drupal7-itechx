<?php
/*
 * Created on Jun 3, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function member_apply($type){
	global $user;
    
    if (1 > $user->uid || '0'==$user->status) {
        $mes = 'You have no right to visit the page. <br />' .
                'Before you visit this page, you need to login and active your account.';
    	return theme('block-promoting', array('title'=>t('Prompting'), 'message'=>$mes));
    }
    
    if (in_array('organizer', $user->roles)) {
    	$vars = array(
            'title' => 'Promoting',
            'mes'   => t('You have been organizer, do not need to apply it again.')
        );
        return theme('block-promoting', $vars);
    }
    
    $retrieved = db_query('select * from {users_organizer} where uid=:uid', array(':uid'=>$user->uid))->fetchAllAssoc('uid');
    
    if (!empty($retrieved)) {
        $organizerInfo = array_pop($retrieved);
        $mesStatus = 'status';
        switch($organizerInfo->verify) {
        	case 'waiting':
                $mes = t('Your application for organizer had commited, Lasooo Team is verifing your information, Please waiting a moment, we will contact you as soon as passible.');
                break;
            case 'success':
                $mes = t('You have been organizer, do not need to apply again.');
                break;
            case 'fail':
                $mes = t('Failed to apply organizer, Please checking your information.');
                $mesStatus = 'error';
                break;
            default:
                $mes = t('Hello, What we can do for you?');
        }
        
        $messages = drupal_get_messages();
        
    	drupal_set_message($mes, $mesStatus);
        if ('success'==$organizerInfo->verify) {
        	return ' ';
        }
    }
    
    return drupal_get_form('member_apply_organizer_form');
}