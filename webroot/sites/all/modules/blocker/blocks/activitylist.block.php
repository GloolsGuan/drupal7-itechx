<?php
/**
 * Block Name: 网站首页主项目导航
 * */
function blocker_block_activitylist_info(){
    return array(
        'info'  => t('Home activity list'),
        /*
            DRUPAL_CACHE_PER_ROLE (default): The block can change depending on the roles the user viewing the page belongs to.
            DRUPAL_CACHE_PER_USER: The block can change depending on the user viewing the page. This setting can be resource-consuming for sites with large number of users, and should only be used when DRUPAL_CACHE_PER_ROLE is not sufficient.
            DRUPAL_CACHE_PER_PAGE: The block can change depending on the page being viewed.
            DRUPAL_CACHE_GLOBAL: The block is the same for every user on every page where it is visible.
            DRUPAL_NO_CACHE: The block should not get cached.
        */
        'cache' => '-1',
        'properties' => 1,
        'weight' => 0,
        /*
            BLOCK_VISIBILITY_NOTLISTED: Show on all pages except listed pages. 'pages' lists the paths where the block should not be shown.
            BLOCK_VISIBILITY_LISTED: Show only on listed pages. 'pages' lists the paths where the block should be shown.
            BLOCK_VISIBILITY_PHP: Use custom PHP code to determine visibility. 'pages' gives the PHP code to use.

        */
        'visibility' => BLOCK_VISIBILITY_NOTLISTED,
        'pages' => null
    );
}

function blocker_block_activitylist_view($delta){
    
    require_once(drupal_get_path('module', 'activity').'/inc_models/activity.model.inc');
    $nodeIds = activity_model_getActivities(30);
    
    return array(
        'subject' => '最受欢迎活动',
        'content' => node_view_multiple($nodeIds, 'frontLatestActivity')
    );
}