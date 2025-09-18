<?php
/*
 * Created on Sep 10, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function activity_forum_node_form_alter(&$form, &$form_state, $form_id){
	//debug_record_data(__FUNCTION__, $form['taxonomy_forums']);
    if (preg_match('/^activity\/([0-9]+)/', request_path(), $matched)) {
    	$activityId = $matched[1];
        
    }else{
    	return NULL;
    }
                        
    $forumContainer  = 'activity_forum_container_'.$activityId;    
    
    $sql = "select b.tid, b.name from {taxonomy_term_hierarchy} as a left join {taxonomy_term_data} as b on(b.tid=a.tid) where a.parent=:formContainerId;";
    
    foreach ($form['taxonomy_forums']['und']['#options'] as $k=>$v) {
    	
        if ($forumContainer==$v) {
    		$forumContainerId = $k;
            break;
    	}
    }
    if (!empty($forumContainerId)) {
    	$queried = db_query($sql, array(':formContainerId'=>$forumContainerId));
        $fetched = $queried->fetchAllAssoc('tid');
    }
    
    if (!empty($fetched)) {
    	$form['taxonomy_forums']['und']['#options'] = array();
        foreach($fetched as $term) {
        	$form['taxonomy_forums']['und']['#options'][$term->tid] = t($term->name);
        }
    }
    
    $form['#submit'][] = "activity_form_submit";
    
    drupal_add_css(path_to_theme(). '/css/components/activity-forum.com.css');
}

function activity_forum_node_form_submit(&$form, &$form_state){
    
    if (preg_match('/^activity\/([0-9]+)/', request_path(), $matched)) {
        $activityId = $matched[1];
        
        $form_state['redirect'] = url('activity/'.$activityId.'/forum');
    }else{
    	
    }
}