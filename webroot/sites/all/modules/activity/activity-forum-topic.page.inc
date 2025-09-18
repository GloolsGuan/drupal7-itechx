<?php
/*
 * Created on 10 Feb, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function activity_forum_topic($activity_id, $topic_id){
	
     $topic = node_load($topic_id);
     
     $activity = node_load($activity_id);
     _activity_forum_build_breadcrumb($activity, $topic);
     drupal_add_css(path_to_theme().'/css/pages/activity-forum-topic.page.css');
     $topic->activity = $activity;
     
     $viewNode = node_view($topic, 'activityTopicFull');
     //_activity_forum_topic_load_comments($topic, $activity);
     
     $objComment = Lib_Glools_Load::load('glools/comment', 'lib');
     
     $topic->test = $objComment->loadComments($topic);
     $topic->testForum = $objComment->getCommentForm($topic);
     
     return $viewNode;
     //return theme('activity-forum-topic', array('activity'=>$activity, 'topic'=>$topic));
}

function _activity_forum_topic_load_comments($node, $activity){
    
      if ($node->comment == COMMENT_NODE_OPEN) {
        $comment_form_location = variable_get('comment_form_location_' . $node->type, COMMENT_FORM_BELOW);
        if (user_access('post comments')) {
          // Show the "post comment" link if the form is on another page, or
          // if there are existing comments that the link will skip past.
          if ($comment_form_location == COMMENT_FORM_SEPARATE_PAGE || (!empty($node->comment_count) && user_access('access comments'))) {
            $links['comment-add'] = array(
              'title' => t('Add new comment'),
              'attributes' => array('title' => t('Share your thoughts and opinions related to this posting.')),
              'href' => 'activity/'.$activity->nid.'/forum/topic/'.$node->nid,
              'fragment' => 'comment-form',
            );
            if ($comment_form_location == COMMENT_FORM_SEPARATE_PAGE) {
              $links['comment-add']['href'] = "comment/reply/$node->nid";
            }
          }
        }
        else {
          $links['comment_forbidden'] = array(
            'title' => theme('comment_post_forbidden', array('node' => $node)),
            'html' => TRUE,
          );
        }
      }
    
    $node->content['links']['comment'] = array(
      '#theme' => 'links__node__comment',
      '#links' => $links,
      '#attributes' => array('class' => array('links', 'inline')),
    );

    // Only append comments when we are building a node on its own node detail
    // page. We compare $node and $page_node to ensure that comments are not
    // appended to other nodes shown on the page, for example a node_reference
    // displayed in 'full' view mode within another node.
    
    $node->content['comments'] = comment_node_page_additions($node);
}