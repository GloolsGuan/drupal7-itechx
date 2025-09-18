<form <?php print drupal_attributes($vars['#attributes']);?>>
<?php if (!empty($vars['#title'])):?>
<div class='title-wrapper'>
    <div class='title'><?php print $vars['#title']?></div>
    <?php if (!empty($vars['#description'])):?>
    <div class='description'><?php print $vars['#description'];?></div>
    <?php endif;?>
    <div class='clearfix'></div>
</div>
<?php endif;?>
<div class='form-wrapper'>
    <?php print $vars['#children'];?>
    <div class='clearfix'></div>
</div>
<div class='clearfix'></div>
</form>