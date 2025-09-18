<div class='block grid_3 block-logo' id='block-logo'>
    <div class='block-title'></div>
    <div class='block-content'>
        <?php if ($logo): ?>
        <a href="<?php print $front_page; ?>" title="旅途如书，书万卷，自有其悟！人生如途，行万里，品味自成。" rel="home" id="logo">
            <img src="<?php print $logo; ?>" alt="<?php print t('Home'); ?>" />
        </a>
        <?php $cityTerm=cache_get('current_city_term'); if(!empty($cityTerm)):?>
        <!--span>(<?php $cityTerm=cache_get('current_city_term'); print $cityTerm->data->name;?>)</span-->
        <?php endif;?>
        <?php endif; ?>
    </div>
</div>
