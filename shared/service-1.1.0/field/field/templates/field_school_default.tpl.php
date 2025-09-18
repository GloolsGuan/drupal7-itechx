<div id='field-<?php print $this->field_name;?>' class='field field-wrapper field-<?php print $this->field_name;?>'>
    <div class='field-header'>
        <h3><?php print $this->label;?></h3>
    </div>
    <div class='field-body form-group form-inline'>
        <?php if (!empty($this->widget['prefix'])): ?>
        <span class='field-prefix'></span>
        <?php endif;?>
        <input class="form-control" name='<?php print $this->field_name;?>' value='<?php print $this->default_value;?>' type='text' placeholder="请输入学校名称" />
        <?php if (!empty($this->widget['suffix'])):?>
        <span class='field-suffix'></span>
        <?php endif;?>
    </div>
    <div class='field-footer'>
        <?php if(true == $this->widget['ajax_submit']):?>
        <button name='save-<?php print $this->field_name;?>' value='保存' >保存</button>
        <?php endif;?>
    </div>
</div>