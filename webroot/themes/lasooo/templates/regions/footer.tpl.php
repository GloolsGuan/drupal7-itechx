<?php if ($page['footer_firstcolumn'] || $page['footer_secondcolumn'] || $page['footer_thirdcolumn'] || $page['footer_fourthcolumn']): ?>
<?php print render($page['footer_firstcolumn']['menu_menu-just-life']); ?>
<?php print render($page['footer_secondcolumn']['menu_menu-lasooo-help']); ?>
<?php print render($page['footer_thirdcolumn']['menu_menu-lasooo-travel']); ?>
<?php print render($page['footer_fourthcolumn']['menu_menu-cooperating-agency']); ?>
<?php endif; ?>
<?php include(THEME_LAS_DIR_TPL . '/blocks/powerby.tpl.php'); ?>
<div class='clearfix'></div>
<?php

$jsCode = glools_attachExtentionJS(null, true);
$s = implode('', $jsCode);
//debug_record_data(__FILE__, $s);
print $s;
?>