<h2><?php print $title;?></h2>
<div class='mes'>
<?php if(!empty($mes)):?>
<?php print $mes;?>
<?php endif;?>
</div>
<?php if (!empty($links)):?>
<?php print render($links);?>
<?php endif;?>