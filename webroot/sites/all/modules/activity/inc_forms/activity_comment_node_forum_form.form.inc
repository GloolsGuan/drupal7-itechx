<?php
/*
 * Created on 20 Feb, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function activity_comment_node_forum_form_alter(&$form, $form_state, $form_id){
	
    debug_rdata(__FUNCTION__, array_keys($form));
    unset($form['actions']['preview']);
    $lang = $form['comment_body']['#language'];
    
    $form['#submit'][] = 'activity_form_submit';
}

function activity_comment_node_forum_form_submit(&$form, &$form_state){
	$topicId = $form_state['build_info']['args'][0]->nid;
    
    
    $container = _activity_load_forumContainer_by_topic($topicId);
    
    $containerArgs = explode('_', $container->name);
    $forumNodeId = array_pop($containerArgs);
    
    $form_state['redirect'] = "activity/$forumNodeId/forum/topic/$topicId";
    
}