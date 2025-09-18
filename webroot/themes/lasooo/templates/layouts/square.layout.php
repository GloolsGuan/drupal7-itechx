<div class='page-wrapper clearfix'>
    <div class='top-wrapper box-wrapper clearfix'>
        <div class='regions-wrapper clearfix'>
            <div id='region-header' class='region region-header clearfix'><?php include(THEME_LAS_DIR_TPL . '/regions/header.tpl.php');?></div>
        </div>
    </div>
    <div class='content-wrapper box-wrapper clearfix'>
        <div class='regions-wrapper clearfix'>
            <div id='region-main' class='region region-main rc_1c_main clearfix'><?php include( THEME_LAS_DIR_TPL . '/regions/region-main-simple.tpl.php');?></div>
        </div>
        <div class='regions-wrapper clearfix'>
            <div id='region-sidebar_left' class='region region-sidebar_left rl_3c_leftSide rl_3c_dr clearfix'><?php print 'hello, This is left side.';?></div>
            <div id='region-sidebar_main' class='region region-sidebar_main rl_3c_main rl_3c_dr clearfix'><?php print 'hello, This is main content.'?></div>
            <div id='region-sidebar_right' class='region region-sidebar_right rl_3c_rightSide clearfix'><?php print 'hello, This is right side.'?></div>
        </div>
    </div>
    <div class='footer-wrapper box-wrapper clearfix'>
        <div class='regions-wrapper clearfix'>
            <div id='region-footer' class='region region-footer clearfix'><?php include(THEME_LAS_DIR_TPL . '/regions/footer.tpl.php');?></div>
        </div>
    </div>
    <div class='extention-wrapper box-wrapper clearfix'>
        <div class='regions-wrapper clearfix'>
            <div id='region-background' class='region region-background'></div>
            <div id='region-extension' class='region region-extension'><?php include(THEME_LAS_DIR_TPL . '/regions/extension.tpl.php');?></div>
        </div>
    </div>
</div>