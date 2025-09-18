<div id='field-<?php print $this->field_name;?>' class='field field-wrapper field-<?php print $this->field_name;?>'>
    <div class='field-header '>
        <h3><?php print $this->label;?></h3>
    </div>
    <div class='field-body form-group'>
        <?php if (!empty($this->widget['prefix'])): ?>
        <span class='field-prefix'><?php print $this->widget['suffix'];?></span>
        <?php else:?>
        <span class='field-prefix'>从</span>
        <?php endif;?>
        <input name='<?php print $this->field_name;?>[min]' type='text' class="input-num" placeholder="最小值" value='<?php print $this->default_value['min'];?>' />
        <?php if (!empty($this->widget['suffix'])):?>
        <span class='field-suffix'><?php print $this->widget['suffix'];?></span>
        <?php endif;?>
    </div>
    <div class='field-body form-group'>
        <span>
        <label class="label"></label>
        <select class='' name='<?php print $this->field_name;?>[rule]'>
            <option value='*' <?php if('*'==$this->default_value['rule']):?>selected='selected'<?php endif;?>>到(并包含)</option>
            <option value='~' <?php if('~'==$this->default_value['rule']):?>selected='selected'<?php endif;?>>到</option>
        </select>
        </span>
    </div>
    <div class='field-body form-group'>
        <?php if (!empty($this->widget['prefix'])): ?>
        <span class='field-prefix'></span>
        <?php endif;?>
        <input name='<?php print $this->field_name;?>[max]' type='text' class="input-num" value="<?php print $this->default_value['max'];?>" placeholder="最大值" />
        <?php if (!empty($this->widget['suffix'])):?>
        <span class='field-suffix'><?php print $this->widget['suffix']; ?></span>
        <?php endif;?>
    </div>
    <div class="field-body">
        <span class='detail'>不设置最大值默认大于最小值,不设置最小值默认小于最大值.</span>
    </div>
    <div class='field-footer'>
        <?php if(true == $this->widget['ajax_submit']):?>
        <button name='save-<?php print $this->field_name;?>' value='保存' >保存</button>
        <?php endif;?>
    </div>
</div>