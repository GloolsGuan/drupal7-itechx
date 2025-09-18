<?php
/*
 * Created on 26 Dec, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function _activity_forum_build_breadcrumb(&$activity, $topic=null){
    $links = array(
        l(t('Home'), null),
        l(t($activity->title), 'activity/'.$activity->nid),
        l(t('forum'), 'activity/'.$activity->nid.'/forum')
    );
    
    if (!empty($topic)) {
        $links[] = l(t($topic->title), 'activity/'.$activity->nid.'/forum/topic/'.$topic->nid);
    }
    
    drupal_set_breadcrumb($links);
}

function _activity_blog_build_breadcrumb(&$activity, $blog=null){
    $links = array(
        l(t('Home'), null),
        l(truncate_utf8($activity->title, 20, true, '...'), 'activity/'.$activity->nid, array('attributes'=>array('title'=>$activity->title))),
        l(t('Blogs'), 'activity/'.$activity->nid.'/blogs')
    );
    
    if (!empty($blog)) {
        $title = truncate_utf8($blog->title, 20, true, '...');
        $links[] = l(t($title), 'activity/'.$activity->nid.'/blog/'.$blog->nid);
    }
    
    drupal_set_breadcrumb($links);
}

function _activity_load_forumContainer_by_topic($topicId){
	if (empty($topicId) || !is_numeric($topicId)) {
		return false;
	}
    
    $term = db_query("select c.tid as tid, c.parent as container_id from {taxonomy_index} as a left join ({taxonomy_term_data} as b, {taxonomy_term_hierarchy} as c) on (a.tid=b.tid and b.tid=c.tid) where a.nid=$topicId limit 1;")->fetchObject();    

    if (!empty($term)) {
        $container = db_query("select * from {taxonomy_term_data} where tid={$term->container_id}")->fetchObject();
    }else{
    	return null;
    }
    
    return $container;
}

function _activity_members($activityInstanceId){
	if (!is_numeric($activityInstanceId)) {
		watchdog('warning', 'Warning: error parameter passed to function '.__FUNCTION__.', Parameters:$activityInstanceId='.$activityInstanceId);
        return false;
	}
    $query = db_query("select user_id from {activity_participant} where instance_id=$activityInstanceId");
    $userIds = $query->fetchAllAssoc('user_id');
    
    
    if (!empty($userIds)) {
        return user_load_multiple(array_keys($userIds));
    }
    return null;
    
}

function activity_agree_participating($aid){
	global $user;
    $queried = db_query('select * from {activity_participant} where user_id=:uid and instance_id=:iid', array('uid'=>$user->uid, 'iid'=>$aid))->fetchAllAssoc('uid');
    if (!empty($queried)) {
    	return true;
    }else{
    	return false;
    }
}