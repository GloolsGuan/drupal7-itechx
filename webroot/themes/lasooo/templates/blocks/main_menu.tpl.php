<div class='block block-main_menu'>
    <div class='block-title'>Main Menu</div>
    <div class='font_text_2 fontcolor_2 block-content'>
        <?php foreach($main_menu as $k=>$menu):?>
        <div class='menu-item menu-item-<?php print $k;?> menu-item-<?php print strtolower(str_replace(' ', '_', $menu['attributes']['title']));?>'><a href='<?php print $menu['href'];?>' title='<?php print $menu['attributes']['title'] ;?>'><?php print $menu['title'];?></a></div>
        <?php endforeach;?>
        <div class='clearfix'></div>
    </div>
</div>