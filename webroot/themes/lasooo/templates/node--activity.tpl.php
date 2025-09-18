<!--  starting node  -->
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
    <div class='activity-content'>
        <div class='title'>
            <h2><?php print $title;?></h2>
        </div>
        <div class='photos'>
            <?php print glools_load_view(array('view'=>'custom/activity/logos'), array('custom/activity/logos'=>array('logos'=>$content['field_activity_ext_logos'])));?>
            <!--div class='photowall'>
                <div class='field-item'>
                    <?php $photo = $content['field_activity_ext_logos'][0]; print render($photo);?></div>
                </div>
            <div class='photos-navigation'>
                <?php print render($content['field_activity_ext_logos']);?>
                <?php //print render($content['field_activity_logo']);?>
            </div-->
        </div>
        <div class='detail'>
            <?php
            print $node->body['und'][0]['value'];
            ?>
            <div class='clearfix'></div>
        </div>
    </div>
    <div class='links'>
        <?php print render($content['links']);?>
    </div>
    <div class='ext-links'>
        <wb:share-button type="button" size="big" relateuid="2624126193" title='<?php print $title. ';&nbsp' . t('@lasooo') ?>' <?php if(isset($content['field_activity_logo']['#object']->field_activity_logo['und'][0]['uri'])):?>pic='<?php print file_create_url($content['field_activity_logo']['#object']->field_activity_logo['und'][0]['uri']) ?>' <?php endif;?> ></wb:share-button>
    </div>
    <div class='comments'>
        <?php print render($content['comments']); ?>
    </div>
<?php
    // Remove the "Add new comment" link on the teaser page or if the comment
    // form is being displayed on the same page.
    if ($teaser || !empty($content['comments']['comment_form'])) {
      //unset($content['links']['comment']['#links']['comment-add']);
    }
    // Only display the wrapper div if there are links.
    //$links = render($content['links']);
    if (isset($links)):
  ?>
    <!--div class="link-wrapper">
        <?php// print $links; ?>
    </div-->
<?php endif; ?>
<?php //print render($content['comments']); ?>
</div><!-- -- end of node -- -->