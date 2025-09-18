<?php
/*
 * Created on Sep 16, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function frontend_preset_js_activity(&$vars, $path){
    
	if ('activity/create'==$path) {
        drupal_add_js('./sites/all/libraries/My97DatePicker/WdatePicker.js');
        drupal_add_js(path_to_theme() . '/js/activity-create.js');
    }
    
    if(preg_match('/^activity\/[0-9]/', $path)) {
    	drupal_add_js('http://tjs.sjs.sinajs.cn/open/api/js/wb.js?appkey=', 'external');
    }
}