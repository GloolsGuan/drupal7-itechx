<?php
/*
 * Created on 16 Feb, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function activity_blogs($aid){
    global $user;
    $activity = node_load($aid);
    
    variable_set('current_activity', $activity);
    
    _activity_blogs_init($aid);
    
    $blogs = _activity_blogs_load(ACTIVITY_BLOG_TERM_PREFIX.$aid);
    
    if (!empty($blogs)) {
    	$build = node_view_multiple($blogs, 'activityBlogTeaser');
        foreach ($build['nodes'] as $nid=>$node) {
            if (isset($build['nodes'][$nid]['links']['blog'])) {
                unset($build['nodes'][$nid]['links']['blog']);
            }
        }
        
        $build['pager'] = array(
          '#theme' => 'pager',
          '#weight' => 5,
        );
    }else{
    	$build['#children'] = 'There is no blog posted now.';
    }
    
    _activity_blog_build_breadcrumb($activity);
    drupal_add_css(path_to_theme() . '/css/activity-blogs.page.css');
    drupal_add_css(path_to_theme() . '/css/components/blogs.com.css');
    
    return theme('activity_blogs', array('blogs'=>$build, 'activity_id'=>$aid));
}


function _activity_blogs_load($termName){
	
	$query = db_select('taxonomy_index', 'a')->extend('PagerDefault');
	
	$query->rightJoin('taxonomy_term_data', 'b', 'b.tid=a.tid');
	
	$query->fields('a', array('nid', 'tid'))
		  ->condition('b.name', $termName)
		  ->orderBy('a.created', 'desc')
		  ->addTag('node_access')
		  ->limit('10');
	
	//$reQuery = $query->execute()->fetchAllAssoc('nid');
    $reQuery = $query->execute();
    
    if ($reQuery->rowCount()>0) {
    	$rows = $reQuery->fetchAllAssoc('nid');
    }
	
    $nodes = array();
    if (is_array($rows) && !empty($rows)) {
        $ids = array_keys($rows);
        foreach ($ids as $id) {
            if (empty($id)) {
            	continue;
            }
            $nodes[$id] = node_load($id);
    	}
    }
    
    return $nodes;
}

/**
 * 
 * */
function _activity_blogs_init($aid){
	$blogTerm = taxonomy_get_term_by_name(ACTIVITY_BLOG_TERM_PREFIX.$aid);
    
    if (empty($blogTerm)) {
    	_activity_blogs_buildTerm($aid);
    }
}

function _activity_blogs_buildTerm($aid){
	
    $vocabulary = db_query("select * from {taxonomy_vocabulary} where machine_name=:name limit 1;", array(':name'=>ACTIVITY_BLOG_VOCABULARY_MACHINENAME))->fetchObject();
    
    //debug_rdata('activity-blogs.page.inc:_activity_blogs_buildTerm', $vocabulary);
    
    $term = array(
        'vid' => $vocabulary->vid,
        'name' => ACTIVITY_BLOG_TERM_PREFIX.$aid,
        'description' => '',
        'format' => 'text',
        'parent' => 0,
        'vocabulary_machine_name' => ACTIVITY_BLOG_VOCABULARY_MACHINENAME
    );
    taxonomy_term_save((object)$term);
    
    return ACTIVITY_BLOG_TERM_PREFIX.$aid;
}