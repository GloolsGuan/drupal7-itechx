<?php
/*
 * Created on 5 Dec, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

return array(
    'frontpage' => array(
        'dynamic_background' => array(
            //array('weight'=>0, 'view'=>'animation/dybg', 'private_key'=>'DynamicBackground')
        ),
        'sidebar_slideshow' => array(
            //array('weight'=>0, 'view'=>'custom/lasooo/navigation', 'private_key'=>'frontpage'),
            //array('weight'=>0, 'view'=>'lasooo/join'),
            //array('weight'=>0, 'view'=>'custom/lasooo/share', 'private_key'=>'frontpage'),
            //array('weight'=>0, 'view'=>'custom/lasooo/uselasooo', 'private_key'=>'frontpage'),
            //array('weight'=>0, 'view'=>'lasooo/about'),
            //array('weight'=>0, 'view'=>'lasooo/slideshow'),
            //array('weight'=>0, 'view'=>'custom/lasooo/welfare', 'private_key'=>'frontpage'),
            //array('weight'=>0, 'view'=>'lasooo/ourlife'),
            //array('weight'=>0, 'view'=>'lasooo/tellyourfriends'),
            //array('weight'=>0, 'view'=>'lasooo/resource'),
            //array('weight'=>0, 'view'=>'lasooo/growingup'),
            //array('weight'=>0, 'view'=>'lasooo/earth')
        ),
        'content' => array(
            //array('weight'=>0, 'view'=>'custom/activities/sicons', 'private_key'=>'frontend', 'tag'=>'PROMOTED_STICKY', 'rows'=>'12'),
            //array('weight'=>0, 'view'=>'custom/activities/icons', 'private_key'=>'frontend', 'tag'=>'MOSTFAVORITE', 'rows'=>'12')
        )
    ),
    'group' => array(),
    'square' => array(
        'content' => array(
            array('weight'=>0, 'view'=>'custom/activities/icons', 'private_key'=>'frontend', 'tag'=>'MOSTFAVORITE', 'rows'=>'12')
        )
    ),
    'home'  => array(
        'sidebar_top' => array(
            array('weight'=>0, 'view'=>'share/menu/normal', 'private_key'=>'homeNavigator', 'featureSuffix'=>'homeNavigator')
        ),
        'sidebar_first'     => array(
            array('weight'=>0, 'view'=>'custom/members/avatar', 'private_key'=>'home/profile'),
            //array('weight'=>0, 'view'=>'custom/members/levelprocess', 'private_key'=>'home/profile'),
        )
    ),
    'home/tab'  => array(
        'sidebar_top' => array(
            array('weight'=>0, 'view'=>'share/menu/normal', 'private_key'=>'homeNavigator', 'featureSuffix'=>'homeNavigator')
        ),        
        'sidebar_first' => array(
            array('weight'=>0, 'view'=>'share/menu/normal', 'private_key'=>'homeTab', 'featureSuffix'=>'homeTab'),
        ),
        'sidebar_second' => array(
            array('weight'=>0, 'view'=>'custom/book/contents')
        )
    ),
    'book' => array(
        'sidebar_second' => array(
            array('weight'=>0, 'view'=>'custom/book/contents')
        )
    ),
    'CityChannel' => array(
        'sidebar_top' => array(
            array('weight'=>0, 'view'=>'custom/activities/slide', 'private_key'=>'promotedActivity')
        ),
        'main' => array(
            array('weight'=>0, 'view'=>'custom/activities/list', 'private_key'=>'LatestActivity')
        ),
        'sidebar_second' => array(
            //array('weight'=>0, 'view'=>'city/card'),
            array('weight'=>0, 'view'=>'custom/tag/activity'),
            //array('weight'=>0, 'view'=>'advertisement/photo'),
            //array('weight'=>0, 'view'=>'activity/periods'),
            array('weight'=>0, 'view'=>'custom/activities/recommended')
        )
    ),
    'CityChannel/Search' => array(
        'main' => array(
            //array('weight'=>0, 'view'=>'custom/activities/list', 'private_key'=>'LatestActivity')
        ),
        'sidebar_second' => array(
            //array('weight'=>0, 'view'=>'city/card'),
            array('weight'=>0, 'view'=>'custom/tag/activity'),
            //array('weight'=>0, 'view'=>'advertisement/photo'),
            //array('weight'=>0, 'view'=>'activity/periods'),
            array('weight'=>0, 'view'=>'custom/activities/recommended')
        )
    ),
    'activity/detail' => array(
        'sidebar_second' => array(
            array('weight'=>0, 'view'=>'custom/members/avatar', 'private_key'=>'activity/owner'),
            //array('weight'=>0, 'view'=>'custom/activity/period'),
            array('weight'=>0, 'view'=>'custom/activity/info'),
            array('weight'=>0, 'view'=>'custom/activity/navigation'),
            array('weight'=>0, 'view'=>'custom/activity/members')
        )
    ),
    'activity/create' => array(
        'sidebar_second' => array(
            array('weight'=>0, 'view'=>'custom/members/avatar', 'private_key'=>'user/current'),
            array('weight'=>0, 'view'=>'share/books/book')
        )
    ),
    'member' => array(
        'sidebar_first' => array(
            array('weight'=>0, 'view'=>'custom/members/avatar', 'private_key'=>'activity/owner'),
            //array('weight'=>0, 'view'=>'share/menu/normal', 'private_key'=>'member/navigator'),
        )
    )
);
