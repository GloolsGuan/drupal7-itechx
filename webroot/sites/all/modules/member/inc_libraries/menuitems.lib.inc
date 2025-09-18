<?php
/*
 * Created on Mar 1, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function _member_load_menu_items($section) {
	$menus = Glools_Inc_Tools::loadData('menuitems', 'data', 'member');
    
    if (!empty($menus[$section])) {
    	return $menus[$section];
    }
    
    return null;
}