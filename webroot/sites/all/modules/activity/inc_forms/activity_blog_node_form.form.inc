<?php
/*
 * Created on 21 Feb, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function activity_blog_node_form_alter(&$form, $form_state, $form_id){
    if (!preg_match('/^activity\/([0-9]+)\/blogs\/post/', request_path(), $matched)) {
    	return ;
    }
    
    $form['#submit'][] = 'activity_form_submit';
    
    $lang = $form['field_activity_term']['#language'];
    
    $terms = taxonomy_get_term_by_name(ACTIVITY_BLOG_TERM_PREFIX . $matched[1]);
    if (empty($terms)) {
    	return;
    }
    $blogTerm = array_pop($terms);
    $form['title']['#title'] = t('Title');
    $form['field_activity_term'][$lang]['#value'] = array($blogTerm->tid);
    
    $form['#activity_info'] = explode('/', request_path());
}

function activity_blog_node_form_submit(&$form, &$form_state){
	
    $activityInfo = $form_state['complete form']['#activity_info'];
    
    if ('activity'==$activityInfo[0]) {
    	$form_state['redirect'] = url('activity/'.$activityInfo[1].'/blogs');
    }
}