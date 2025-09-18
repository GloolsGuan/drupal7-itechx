<div class='blogs-extensions clearfix'>
    <div class='ext'><?php print l(t('New blog'), 'activity/'.$activity_id.'/blogs/post');?></div>
</div>
<div class='blogs'>
    <?php
    if (is_string($blogs)) {
        echo '<div class="messages">' . $blogs . '</div>';
    }else{
        print render($blogs);
    }
    ?>
</div>