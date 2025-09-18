<div class='block block-promoting'>
    <div class='block-title'><?php print $title;?></div>
    <div class='block-content'>
        <div class='mes'><?php print $mes;?></div>
        <?php if (!empty($links)):?>
        <?php print render($links);?>
        <?php endif;?>
        <div class='clearfix'></div>
    </div>
</div>