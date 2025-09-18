<!--  starting node  -->
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
    <div class='activity-content'>
        <div class='title'>
            <?php print l($title, 'activity/'.$node->nid);?>
        </div>
        <a href='<?php print url('activity/'.$node->nid)?>'><?php print render($content['field_activity_logo']);?></a>
        <div class='detail'>
            <?php
            print truncate_utf8(strip_tags($node->body['und'][0]['summary']), 100, true, '...');
            ?>
            <div class='clearfix'></div>
        </div>
    </div>
</div><!-- -- end of node -- -->