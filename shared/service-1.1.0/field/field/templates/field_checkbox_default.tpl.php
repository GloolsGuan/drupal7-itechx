<div id='field-<?php print $this->field_name;?>' class='field field-wrapper field-<?php print $this->field_name;?>'>
    <div class='field-header '>
        <h3><?php print $this->label;?></h3>
    </div>
    <div class='field-body'>
        
        <?php if (!empty($this->widget['prefix'])): ?>
        <span class='field-prefix'></span>
        <?php endif;?>
        <?php if('checkbox'==$this->widget['type']):?>
            <?php foreach($this->values as $k=>$value):?>
            <?php if(!empty($this->default_value) && in_array($k, $this->default_value)):?>
            <div class="form-inline form-group"><input type='checkbox'  name='<?php print $this->field_name;?>[<?php print $k;?>]' checked="checked" value='<?php print $k;?>' /><label><?php print $value['value'];?></label></div>
            <?php else:?>
            <div class="form-inline form-group"><input type='checkbox'  name='<?php print $this->field_name;?>[<?php print $k;?>]' value='<?php print $k;?>' /><label><?php print $value['value'];?></label></div>
            <?php endif;?>
            <?php endforeach;?>
        <?php else:?>
            <?php foreach($this->values as $k=>$value):?>
            <?php if(!empty($this->default_value) && in_array($k, $this->default_value)):?>
            <div class='form-inline form-group'><input type='radio'  name='<?php print $this->field_name;?>' checked="checked" value='<?php print $k;?>' /><label><?php print $value['value'];?></label></div>
            <?php else:?>
            <div class="form-inline form-group"><input type='radio'   name='<?php print $this->field_name;?>' value='<?php print $k;?>' /><label><?php print $value['value'];?></label></div>
            <?php endif;?>
            <?php endforeach;?>
        <?php endif;?>
        <?php if (!empty($this->widget['suffix'])):?>
        <span class='field-suffix'></span>
        <?php endif;?>
    </div>
    <div class='field-footer'>
        <?php if(true == $this->widget['ajax_submit']):?>
        <button name='save-<?php print $field['field_name'];?>' value='保存' >保存</button>
        <?php endif;?>
    </div>
</div>