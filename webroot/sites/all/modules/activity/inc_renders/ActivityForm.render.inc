<?php
/*
 * Created on 11 Dec, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function activity_render_ActivityForm($activity=null) {
  
    require_once(drupal_get_path('module', 'node').'/node.pages.inc');
    
    return node_add('activity');
}