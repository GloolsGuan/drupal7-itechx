<?php
/*
 * Created on Sep 14, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 * 
 * if ('book'==$node['body']['#object']->type) {
        $node['title'] = array(
            '#prefix' => '<div class="title"><h2>',
            '#children' => $node['body']['#object']->title,
            '#suffix' => '</h2></div>'
        );
    }
 */
function frontend_node_book_view_alter(&$node){
	$node['title'] = array(
        '#prefix' => '<div class="title"><h2>',
        '#children' => $node['body']['#object']->title,
        '#suffix' => '</h2></div>'
    );
}