<!--div class='block block-member-info'>
    <div class='block-title'><?print t("General Information");?><?php print l('Edit', 'member/edit/general');?></div>
    <div class='block-content'>
        <div class='photo'><?php print member_load_member_photo($user);?></div>
        <div class='name'><?php print $user->name;?></div>
        <div class='signature'><?php print $user->signature;?></div>
        <div class='created'><?php print t('Registered on').' '. date("Y-m-d", $user->created);?></div>
        <div class='roles'>
            <span class='title'><?php print t('User level');?></span>
            <?php foreach($user->roles as $role):?>
            <span class='level'><?php print $role;?></span>
            <?php endforeach;?>
        </div>
    </div>
</div-->
<!--div class='block block-member-points'>
    <div class='block-title'><?php print t('Points');?><?php print l('Edit', 'member/edit/points');?></div>
    <div class='block-content'>
        用户当前积分状况开发中...
    </div>
</div-->
<div class='block block-member-contact'>
    <div class='block-title'><?php print t('Contact');?><?php print l('Edit', 'member/edit/contact');?></div>
    <div class='block-content'>
        <?php if (is_array($contact)):?>
        <?php foreach ($contact as $k=>$v):?>
        <?php if('uid'==$k || 'varified_item'==$k):continue;endif;?>
        <div class='item'>
            <div class='title'><?php print t($k);?></div>
            <div class='content'><?php print $v;?></div>
        </div>
        <?php endforeach;?>
        <?php else:?>
        <?php print t('You has not set contact information,Before you join or startup a activity you should set your contact information.');?>
        <?php endif;?>
    </div>
</div>
