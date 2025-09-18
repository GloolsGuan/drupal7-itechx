<div <?php print drupal_attributes($variables['#attributes']);?> >
    <div class='block-title'><?php print $subject;?></div>
    <div class='block-content'>
        <?php print render($content);?>
        <div class=''clearfix></div>
    </div>
</div>