<?php
/*
 * Created on 10 Feb, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function activity_forum_posttopic($aid){
	//var_dump($_POST);
    require_once(drupal_get_path('module', 'node').'/node.pages.inc');
    return node_add('forum');
}