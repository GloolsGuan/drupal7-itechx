<?php
/*
 * Created on Jun 25, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function _frontend_preset_share_menu_normal($view){
	
    $hook = _frontend_preset_share_menu_maps($view['private_key']);
    
    if (function_exists($hook)) {
    	return $hook($view);
    }
}

function _frontend_preset_share_menu_maps($key){
	$maps = array(
        'homeNavigator' => '_frontend_preset_share_menu_homeNavigator',
        'homeTab'       => '_frontend_preset_share_menu_homeTab',
    );
    
    if (isset($maps[$key])) {
    	return $maps[$key];
    }
}

function _frontend_preset_share_menu_homeNavigator($view){
	
    $vars = array(
        'type'        => 'ul',
        'attributes'  => array(),
        'cAttributes' => array(
            'class' => array('homeNavigator')
        ),
        'items' => array(
            array('data'=>theme_link(array('path'=>'home', 'text'=>t('My Home'), 'options'=>array())), 'attributes'=>array('class'=>array('hn-home'))),
            array('data'=>theme_link(array('path'=>'home/message', 'text'=>t('Message'), 'options'=>array())), 'attributes'=>array('class'=>array('hn-message'))),
            array('data'=>theme_link(array('path'=>'home/activity', 'text'=>t('Activity'), 'options'=>array())), 'attributes'=>array('class'=>array('hn-activity'))),
            //array('data'=>theme_link(array('path'=>'home/groups', 'text'=>t('Groups'), 'options'=>array())), 'attributes'=>array('class'=>array('hn-group'))),
            //array('data'=>theme_link(array('path'=>'home/account', 'text'=>t('Account'), 'options'=>array())), 'attributes'=>array('class'=>array('hn-account'))),
            //array('data'=>theme_link(array('path'=>'home/inns', 'text'=>t('Inns'), 'options'=>array())), 'attributes'=>array('class'=>array('hn-inns')))
        )
    );
    
    return theme('glools_item_list', $vars);
}


function _frontend_preset_share_menu_homeTab($view){
	
    $pArgs = explode('/', request_path());
    
    if (count($pArgs)>1) {
    	$tab = $pArgs[1];
    }
    if (empty($tab)) {
    	return null;
    }
    
    $tabs = module_invoke($tab, 'homeTabs');
    
    return $tabs;
}

