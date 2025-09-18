<?php
/*
 * Created on 11 Dec, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

define('ACTIVITY_FORUM_CONTAINER_PREFIX', 'activity_forum_container_');


function activity_forum($aid) {
    
    if (!module_exists('forum')) {
    	drupal_set_message('Forum module is required when you active activity module.', 'warning');
        return false;
    }
    
    $activity = node_load($aid);
    
	_activity_init_forum($aid);
    $data = _activity_load_forum($aid);
    $re = array();
    $re['header'] = array(
        '#header' => $data['container'],
        '#theme' => 'activity_forum_pageheader',
        '#activity' => &$activity
    );
    
    $re['topics'] = array();
    $forum = array_shift($data['forum_info']->forums);
    /*
    foreach($data['forum_info']->forums as $tid=>$forum){
    	$re[$tid] = array(
            '#theme' => 'activity_forum_topics',
            '#topics' => forum_get_topics($tid, 'created', 20),
            '#forum' => $forum,
            '#activity' => &$activity,
            '#pager' => theme('pager')
        );
    }*/
    $buildForum = array(
        '#theme' => 'activity_forum_topics',
        '#topics' => _activity_forum_get_topics($forum->tid, 'created', 20),
        '#forum' => $forum,
        '#activity' => &$activity,
        '#pager' => theme('glools_pager', array('element'=>'activity-forum', 'weight'=>5))
    );
    drupal_add_css(path_to_theme().'/css/pages/activity-forum.page.css');
    _activity_forum_build_breadcrumb($activity);
    
    //return $re;
    return $buildForum;
}


function _activity_load_forum($aid){
    $data = array();
	$taxonomy = taxonomy_get_term_by_name(ACTIVITY_FORUM_CONTAINER_PREFIX.$aid);
    if (count($taxonomy)>0) {
    	$data['container'] = array_shift($taxonomy);
    }
    //var_dump($data['container']);
    
    $data['forum_info'] = forum_forum_load($data['container']->tid);
    $data['topics'] = array();
    
    foreach($data['forum_info']->forums as $forum){
    	$data['topics'][$forum->tid] = forum_get_topics($forum->tid, 'created', 20);
    }
    
    return $data;
}

/**
 * Check weather the activity forum has been created, if not, create it.
 * */
function _activity_init_forum($aid) {
    
    $forums = taxonomy_get_term_by_name(ACTIVITY_FORUM_CONTAINER_PREFIX.$aid);
    if (empty($forums)) {
    	_activity_create_forum_container(ACTIVITY_FORUM_CONTAINER_PREFIX.$aid);
    }
}

function _activity_create_forum_container($containerName) {
	$termContainer = (object) array(
        'name' => $containerName,
        'description' => 'forum description.',
        'parent' => array('0'),
        'weight' => 0,
        'vid' => variable_get('forum_nav_vocabulary', 0)
    );
    $status = taxonomy_term_save($termContainer);
    
    // Set the term as a forum container
    $containers = variable_get('forum_containers', array());
    $containers[] = $termContainer->tid;
    variable_set('forum_containers', $containers);
    
    _activity_create_default_forum($termContainer);
    
    return $status;
}

function _activity_create_default_forum($container){
	$forum = (object) array(
        'name' => t('Activity Forum'),
        'description' => 'Default activity forum.',
        'parent' => array($container->tid),
        'weight' => 0,
        'vid' => variable_get('forum_nav_vocabulary', 0)
    );
    
    return taxonomy_term_save($forum);
}

function _activity_forum_get_topics($tid, $sortby, $forum_per_page) {
  global $user, $forum_topic_list_header;

  $forum_topic_list_header = array(
    NULL,
    array('data' => t('Topic'), 'field' => 'f.title'),
    array('data' => t('Replies'), 'field' => 'f.comment_count'),
    array('data' => t('Last reply'), 'field' => 'f.last_comment_timestamp'),
  );

  $order = _forum_get_topic_order($sortby);
  for ($i = 0; $i < count($forum_topic_list_header); $i++) {
    if ($forum_topic_list_header[$i]['field'] == $order['field']) {
      $forum_topic_list_header[$i]['sort'] = $order['sort'];
    }
  }
  
  $query = db_select('forum_index', 'f')->extend('GloolsPagerDefault')->extend('TableSort');
  $query->fields('f');
  $query->condition('f.tid', $tid)
        ->addTag('node_access')
        //->orderBy('f.last_comment_timestamp', 'DESC')
        //->orderBy('f.created', 'DESC')
        ->orderByHeader($forum_topic_list_header)
        ->limit($forum_per_page)
        ->addMetaData('page-owner','activity-forum');
    

  $count_query = db_select('forum_index', 'f');
  $count_query->condition('f.tid', $tid);
  $count_query->addExpression('COUNT(*)');
  $count_query->addTag('node_access');

  $query->setCountQuery($count_query);
  $result = $query->execute();
  $nids = array();
  foreach ($result as $record) {
    $nids[] = $record->nid;
  }

  if ($nids) {
    $query = db_select('node', 'n')->extend('TableSort');
    $query->fields('n', array('title', 'nid', 'type', 'sticky', 'created', 'uid'));
    $query->addField('n', 'comment', 'comment_mode');

    $query->join('node_comment_statistics', 'ncs', 'n.nid = ncs.nid');
    $query->fields('ncs', array('cid', 'last_comment_uid', 'last_comment_timestamp', 'comment_count'));

    $query->join('forum_index', 'f', 'f.nid = ncs.nid');
    $query->addField('f', 'tid', 'forum_tid');

    $query->join('users', 'u', 'n.uid = u.uid');
    $query->addField('u', 'name');

    $query->join('users', 'u2', 'ncs.last_comment_uid = u2.uid');

    $query->addExpression('CASE ncs.last_comment_uid WHEN 0 THEN ncs.last_comment_name ELSE u2.name END', 'last_comment_name');
   
    $query->orderBy('f.last_comment_timestamp', 'DESC')
          ->orderBy('f.created', 'DESC')
          ->orderByHeader($forum_topic_list_header)
          ->condition('n.nid', $nids);
    $result = $query->execute();
  }
  else {
    $result = array();
  }

  $topics = array();
  $first_new_found = FALSE;
  foreach ($result as $topic) {
    if ($user->uid) {
      // A forum is new if the topic is new, or if there are new comments since
      // the user's last visit.
      if ($topic->forum_tid != $tid) {
        $topic->new = 0;
      }
      else {
        $history = _forum_user_last_visit($topic->nid);
        $topic->new_replies = comment_num_new($topic->nid, $history);
        $topic->new = $topic->new_replies || ($topic->last_comment_timestamp > $history);
      }
    }
    else {
      // Do not track "new replies" status for topics if the user is anonymous.
      $topic->new_replies = 0;
      $topic->new = 0;
    }

    // Make sure only one topic is indicated as the first new topic.
    $topic->first_new = FALSE;
    if ($topic->new != 0 && !$first_new_found) {
      $topic->first_new = TRUE;
      $first_new_found = TRUE;
    }
    
    if ($topic->comment_count > 0) {
      $last_reply = new stdClass();
      $last_reply->updated = $topic->last_comment_timestamp;
      $last_reply->name = $topic->last_comment_name;
      $last_reply->uid = $topic->last_comment_uid;
      $topic->last_reply = $last_reply;
    }
    $topics[] = $topic;
  }

  return $topics;
}