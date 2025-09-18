<div class=''>
    <div class='contact messages status help'>
        <h2>你的联系信息是有效的吗？</h2>
        <div class='contact'>
            <?php if (empty($contact)):?>
            你还没有设置有效的联系信息，请<a class='ajax-link' href='#' >设置联系信息</a>
            <?php else:?>
                <?php foreach($contact as $item=>$value):?>
                <?php if(in_array($item, array('uid', 'idcard', 'lasooo_card', 'spare_mobile', 'safe_email', 'varified_item'))):?>
                <?php continue;?>
                <?php endif;?>
                <table>
                    <tr><td width='160' class='title'><?php print t($item);?></td><td class='content'><?php print $value;?></td></tr>
                </table>
                <?php endforeach;?>
                <a class='ajax-link' href='<?php print url('member/contact/edit');?>' >设置联系信息</a>
            <?php endif;?>
        </div>
    </div>
    <h2>知道吗？</h2>
    <div><span>积分</span>&nbsp;参加活动获得的经验积分和诚信积分积累，可以使你有机会参加更多有趣的活动。</div>
    <div><span>贡献</span>&nbsp;你对该活动的赞助支持，可以提高你的贡献积分，同时也很好的支持优质活动的持续发展。</div>
    <div><span>发展</span>&nbsp;当你的级别达到一定程度后，你可以组织自己的活动。</div>
</div>