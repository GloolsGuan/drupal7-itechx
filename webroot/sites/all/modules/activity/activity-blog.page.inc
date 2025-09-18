<?php
/*
 * Created on 21 Feb, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function activity_blog($aid, $bid){
	
    drupal_add_css(path_to_theme() . '/css/components/blog.com.css');
    
    
    $nodes = node_load_multiple(array($bid));
    
    $blog = array_pop($nodes);
    $activity = node_load($aid);
    
    //debug_record_data(__FUNCTION__, $blog);
    _activity_blog_build_breadcrumb($activity, $blog);
    
    return node_view($blog, 'full');
}

function activity_blog_post($aid){
    global $user;
    require_once(drupal_get_path('module', 'node') . '/node.pages.inc');
    
    drupal_set_title(t('Create activity blog'));
    
    drupal_add_css(path_to_theme() . '/css/components/post-blog.com.css');
    
    return node_add('blog');
    /*
    $terms = taxonomy_get_term_by_name(ACTIVITY_BLOG_TERM_PREFIX.$aid);
    if (!empty($terms)) {
        $blogTerm = array_pop($terms);
    }
    
    $node = array(
        'type' => 'blog',
        'uid' => $user->uid,
        'name' => (isset($user->name) ? $user->name : ''),
        'language' => LANGUAGE_NONE
    );
    
    drupal_set_title(t('Create activity blog'));
    
    drupal_add_css(path_to_theme() . '/css/components/post-blog.com.css');
    
    $build = drupal_get_form('blog_node_form', (object)$node);
    $build['language'] = array(
        '#type' => 'value',
        '#value' => 'zh-hans'
    );
    
    $lang = $build['field_activity_term']['#language'];
    
    $build['field_activity_term'][$lang]['#value'] = array($blogTerm->tid);
    /*
    $build['field_activity_term'][$lang] = array(
        '#type' => 'value',
        '#value' => ACTIVITY_BLOG_TERM_PREFIX.$blogTerm->tid
    );*/
    //debug_record_data(__FILE__.':'.__FUNCTION__, $build['field_activity_term'][$lang]);
    //return $build;
    
}
