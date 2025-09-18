<div id='field-<?php print $this->field_name;?>' class='field field-wrapper field-<?php print $this->field_name;?>'>
    <div class='field-header '>
        <h3><?php print $this->label;?></h3>
    </div>
    <div class='field-body form-group'>
        <?php if (!empty($this->widget['prefix'])): ?>
        <span class='field-prefix'></span>
        <?php endif;?>
        <select class="form-control" name='<?php print $this->field_name;?>' <?php if (true===$this->multiple_values):?> size='5' multiple='multiple' <?php endif;?>>
            <option value='0'>选择</option>
            <?php foreach($this->values as $id=>$value): ?>
            <option value='<?php print $id;?>' <?php if($id==$this->default_value):?> selected='selected'<?php endif;?> ><?php print $value['value'];?></option>
            <?php endforeach;?>
        </select>
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