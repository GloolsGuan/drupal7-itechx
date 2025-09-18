<form <?php drupal_attributes($element['#attributes']); ?> >
    <?php print  drupal_render($element['title']);?>
    <div class='activity-pics fieldset-wrapper'>
        <?php print  drupal_render($element['field_logo_thumbnail']);?>
        <?php print  drupal_render($element['field_activity_logo']);?>
        <br style="clear:both;" />
    </div>
    <?php print drupal_render_children($element);?>
</form>