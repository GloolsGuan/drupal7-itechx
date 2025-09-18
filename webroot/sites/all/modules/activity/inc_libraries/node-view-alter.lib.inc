<?php
/*
 * Created on Feb 27, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function activity_activity_node_view_alter(&$build, $type) {
	
	if ('activity' != $build['#node']->type) {
    	return null;
    }
    
    drupal_add_css(path_to_theme().'/css/components/comments.com.css');
    
    $objComment = Lib_Glools_Load::load('glools/comment', 'lib');
     
    $build['comments'] = $objComment->loadComments($build['#node']);
    $build['comments']['comment_form'] = $objComment->getCommentForm($build['#node']);
    
    //$build['links'] = array('#children'=>'hello,world');
    if ('hlist'==$build['#view_mode']) {
    	$build['operations'] = activity_loadOperations($build['#node']);
    }
}

/**
 * Description:
 *   For activity blog detail content display. add activity link to the header of blog content region.
 * */
function activity_blog_node_view_alter(&$build, $type) {
	
    if (!node_is_page($build['#node'])) {
    	return null;
    }
    
    if (empty($build['#node']->field_activity_term) || !preg_match('/^blog\/[0-9]+/', request_path())) {
    	return null;
    }
    
    drupal_add_css(path_to_theme().'/css/components/blog.com.css');
    
    /*
    var_dump($build['#node']->field_activity_term[$build['#node']->language][0]['tid']);
    $term = $build['#node']->field_activity_term[$build['#node']->language][0]['taxonomy_term'];
    $termId = $build['#node']->field_activity_term[$build['#node']->language][0]['tid'];
    */
    
    $termArgs = explode('_', $term->name);
    $activity = node_load($termArgs[3]);
	
	$build['links']['activity'] = array(
		'#theme' => 'links_node_activity',
		'#links' => array(
			'activity' => array(
				'title'=> t('Go back to activity blogs'), 
				'href'=> 'activity/'.$termArgs[3].'/blogs',
				'attributies'=>array('title'=>'activity blogs')
			)
		)
	);

	$build['headerlinks'] = array(
		'#theme' => 'links__node',
		'#pre_render' => array(
			'drupal_pre_render_links'
		),
		'#attributes' => array(
			'class' => array('headerlinks', 'inline')
		),
		'activity' => array(
			'#theme' => 'links_node_activity',
			'#links' => array(
				'activity' => array('title'=> t('Activity').': '.truncate_utf8($activity->title, 34, true, '...'), 'href'=> 'activity/'.$termArgs[3], 'attributies'=>array('title'=>$activity->title))
			)
		)
	);
	
}