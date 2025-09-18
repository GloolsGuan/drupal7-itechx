<?php
/*
if (!empty($node)){
    if (file_exists(dirname(__FILE__).'/node-'.$node->type.'.page.tpl.php')) {
    	include(dirname(__FILE__).'/node-'.$node->type.'.page.tpl.php');
    }else{
    	include(dirname(__FILE__).'/node.page.tpl.php');
    }
}else{
    if(!empty($messages)) {
    	print $messages;
    }
    print render($page['content']);
    
}
*/
if (!empty($breadcrumb)) {
	print '<div class="breadcrumb">'. _lasooo_show_breadcrumb($breadcrumb).'</div>';
}
if (!empty($messages)) {
	echo '<div id="messages">';
    print $messages;
    echo '</div>';
}
if (!empty($page['main'])) {
	print _theme_lasooo_showBlocks($page['main']);
}else{
	print _theme_lasooo_showBlocks($page['content']);
}
