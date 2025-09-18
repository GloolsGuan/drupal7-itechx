<?php
/*
 * Created on Mar 21, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function member_register($type=null){
    global $user;
    
    if (0==$user->uid) {
        $typeName = !empty($type) ? '<span>'.member_register_type($type) . '</span>' : '';
        $mes = 'Thanks you for applying lasooo advance user, before you register as a advance member, You should be a normal registered member first.' .
                        ', It is super easy to register, Just click the following link, Thanks for your support!';
        $vars = array(
            'title' => t('Thanks for you register as a advance user') .' ' . $typeName,
            'mes' => t($mes),
            'links' => theme('links', array(
                'links' => array(
                    array('href'=>'user/register', 'title'=>t('Register a member')),
                    array('href'=>'book/', 'title'=>t('Learning more about lasooo member'))
                )   
            )),
            'heading' => array()
            
        );
        drupal_add_css(path_to_theme() . '/css/blocks/simple.block.css');
    	return Glools_Inc_Tools::renderTpl($vars, 'register', 'member');
    }
	return 'Welcome to register as a ' . $type;
}

function member_register_type($type){
	$types = array(
        'leader'    => t('Leader'),
        'organizer' => t('Organizer'),
        'supporter' => t('Supporter'),
        'scholar'   => t('Scholar'),
        'trainer'   => t('Trainer')
    );
    
    if (isset($types[$type])) {
    	return $types[$type];
    }
}