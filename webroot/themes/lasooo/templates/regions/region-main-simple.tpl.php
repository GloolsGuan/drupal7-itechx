<?php
/*
    Simple region layout:
    There is no breadcrumb content region.

**/
if (!empty($messages)) {
	echo '<div id="messages">';
    print $messages;
    echo '</div>';
}
if (!empty($page['main'])) {
	print render($page['main']);
}else{
	print render($page['content']);
}
