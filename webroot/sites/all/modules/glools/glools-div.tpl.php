<div <?php print drupal_attributes($vars['attributes']);?>>
    <?php if (!empty($vars['title'])):?>
        <h2><?php print $vars['title'];?></h2>
    <?php endif;?>
    <?php print $vars['content'];?>
</div>