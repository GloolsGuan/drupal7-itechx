<?php
/**
 * About the sample program
 * 1. you can build new block program by this file, just copy it and modify some code.
 * 2. The real file name of block program should be [BlockName].block.php.
 * 3. Block hook name prefix rule is [ModuleName]_block_[BlockName]_[Drupal block hook suffix], 
 *    for example: 
 *    "test_block_sample_info" will be invoke by test_block_info. 
 *    "test_block_sample_configure" will be invoke by test_block_configure.
 *    "test_block_sample_view" will be invoke by test_block_view.
 *    "test_block_sample_save" will be invoke by test_block_save.
 * 
 * More information about block information at:
 * @link http://api.drupal.org/api/drupal/modules--block--block.api.php/function/hook_block_info/7
 * */
 
/**
 * Default delta name is 'test_block_sample'
 * */
function blocker_block_sample_info(){
	return array(
        'info'  => t('Sample block of advanced block engine.'),
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

function blocker_block_sample_view($delta){
	return array(
        'subject' => t('Sample block of test module'),
        'content' => 'hello,world, This is sample block of test module.'
    );
}