<div id='field-<?php print $this->field_name;?>' class='field field-wrapper field-<?php print $this->field_name;?>'>
    <div class='field-header '>
        <h3><?php print $this->label;?></h3>
    </div>
    <div class='field-body'>
        <select id='field-address-province' class='address-province'  name='<?php print $this->field_name;?>[province]'>
            <option value='000000'>省份</option>
            <?php foreach($this->values as $zid=>$zone):?>
            <?php if ('0'==$zone['rank']):?>
                <?php if (!empty($this->default_value['province']) && $zid==$this->default_value['province']):?>
            <option value="<?php print $zid;?>" selected="selected"><?php print $zone['zone_name'];?></option>
                <?php else:;?>
            <option value="<?php print $zid;?>" ><?php print $zone['zone_name'];?></option>
                <?php endif;?>
            <?php endif;?>
            <?php endforeach;?>
        </select>
        <select id='field-address-city' class='address-city' name='<?php print $this->field_name;?>[city]'>
            <option value='000000'>城市</option>
            <?php if (!empty($this->default_value['city']) && array_key_exists($this->default_value['city'], $this->values)):?>
            <?php $city = $this->values[$this->default_value['city']];?>
            <option value='<?php print $this->default_value['city'];?>' selected="selected"><?php print $city['zone_name'];?></option>
            <?php endif;?>
        </select>
        <select id='field-address-area' class='address-area' name='<?php print $this->field_name;?>[area]'>
            <option value='000000'>区</option>
            <?php if (!empty($this->default_value['area']) && array_key_exists($this->default_value['area'], $this->values)):?>
            <?php $area = $this->values[$this->default_value['area']];?>
            <option value='<?php print $this->default_value['area'];?>' selected="selected"><?php print $area['zone_name'];?></option>
            <?php endif;?>
        </select>
        <div class='address-detail'>
            <input id='field-address-detail' name='<?php print $this->field_name;?>[detail]' type='text' class='form-control address-detail' value='<?php print $this->default_value['detail'];?>' placeholder="详细地址" />
        </div>
    </div>
    <div class='field-footer'>
        <?php if(true == $this->widget['ajax_submit']):?>
            <button name='save-address' value='保存' >保存</button>
        <?php endif;?>
    </div>
</div>
<script type='text/javascript'>
;(function($){
    var field_wrapper = '#field-<?php print $this->field_name;?>';
    var zones = <?php print json_encode($this->values);?>;
    //-- Append options to some place --
    var buildList = function(level, c_field){
        var n_level = ('province'==level) ? 'city' : 'area';
        var select = $(field_wrapper).find('select.address-'+n_level);
        
        //select.find('option[value!="000000"]').empty();
        select.empty();
        if ('city'==n_level){
            select.append("<option value='000000'>城市</option>");
        } else if ('area'==n_level) {
            select.append("<option value='000000'>区/县</option>");
        }
        
        select.append(c_field);
    }
    
    
    var loadZones = function(parent_id, level){
        
        $.get('/fields/loadAddress',{'parent_id':parent_id}, function(rsp, status, xhr){
        loadedZones = rsp;
        var c_field = [];
        for(z in loadedZones) {
            //console.log();
            if (loadedZones[z]['zone_id']==parent_id) {
                continue;
            }
            c_field.push($("<option value='"+ loadedZones[z].zone_id +"'>"+loadedZones[z].zone_name+"</option>"));
        }
        //console.log(c_field);
        buildList(level, c_field);
        
        }, 'json');
        
    }
    
    
    
    $(document).ready(function(){
        //console.log(['field-address testing - 11', field_wrapper, $(field_wrapper).find('select')]);
        $(field_wrapper).find('select').bind('change',function(e){
            //console.log(this);
            var changedId = $(this).attr('id');
            var level = changedId.split('-')[2];
            var id = $(this).val();
            var zone_field = zones[id];
            var c_field = [];
            if ('province'==level || 'city'==level) {
                
                //-- If province selected again, area should be changed back to original. --
                if ('province'==level) {
                    //console.log($(field_wrapper).find('select[name="area"]').length);
                    $(field_wrapper).find('.address-area').empty();
                    $(field_wrapper).find('.address-area').append($("<option value='000000'>区/县</option>"));
                }
                
                //-- Seeking for zones from zone dictionary.
                loadZones(id, level);
            }
        });
    });
})(jQuery);

</script>