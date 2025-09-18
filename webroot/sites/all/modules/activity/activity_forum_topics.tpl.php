<?php
/*
 * Created on 10 Feb, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
/**
 * Defined variables:
 * $forum : forum information.
 * $topic : topic data list.
 * $activity : activity node information.
 * */
?>
<div id='block-activity_forum_topic' class='block block-activity_forum_topic'>
    <!--div class='block-title'><?php print $forum->name?><br /><span><?php print $forum->description;?></span></div-->
    <div class='block-content clearfix'>
        <div class='topic topic-extension clearfix'>
            <div><a href='/activity/<?php print $activity->nid;?>/forum/post-topic'>发言</a></div>
        </div>
        <div class='topic topic-header'>
            <div class='title'><?php print t('Title');?></div>
            <div class='author'><?php print t('Author');?></div>
            <div class='reviews'><?php print t('Comments');?></div>
            <div class='last-review'><?php print t('Last Comment at')?></div>
            <div class='last-review-at'><?php print t('Updated at')?></div>
        </div>
        <?php foreach($topics as $id=>$topic):?>
        <?php
            $today = date('Y-m-d', time());
            $topicTime = date('Y-m-d H:i', $topic->last_comment_timestamp);
            $topicTimeArgs = explode(' ', $topicTime);
            $freshTopic = false;
            if ($today==$topicTimeArgs[0]) {
                $freshTopic = true;
                $lastUT = $topicTimeArgs[1];
            }else{
                $lastUT = $topicTimeArgs[0];
            }
            
        ?>
        <div class='topic'>
            <div class='title'><a href='/activity/<?php print $activity->nid?>/forum/topic/<?php print $topic->nid?>'><?php print $topic->title;?></a></div>
            <div class='author'><?php $user = user_load($topic->uid); ?><?php print l($user->name, 'member/'.$user->uid)?></div>
            <div class='reviews'><?php print $topic->comment_count;?></div>
            <div class='last-review'><?php print l($topic->last_comment_name, 'member/'.$topic->last_comment_uid);?></div>
            <div class='last-review-at'><?php print $lastUT;?></div>
        </div>
        <?php endforeach;?>
        <div class='pager'><?php print $pager;?></div>
    </div>
</div>