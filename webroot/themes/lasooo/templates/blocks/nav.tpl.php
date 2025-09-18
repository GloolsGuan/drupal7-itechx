<div class='block grid_7 block-nav' id='block-nav'>
    <div class='block-title'></div>
    <div class='block-content'>
        <div class='nav-item nav-item-home <?php if('home'==request_path()): print 'active-nav'; endif;?>'><a href='/home'><?php print t('HOME');?></a></div>
        <div class='nav-item nav-item-square <?php if('square'==request_path()): print 'active-nav'; endif;?>'><a href='<?php print url('square')?>'><?php print t('SQUARE');?></a></div>
        <div class='nav-item nav-item-group <?php if('group'==request_path()): print 'active-nav'; endif;?>'><a href='/group'><?php print t('GROUP');?></a></div>
    </div>
</div>
