

jQuery(document).ready(function(){
    var fieldItems = jQuery('.node-activity .photos .field-name-field-activity-ext-logos .field-items');
    
    //jQuery('.node-activity .photos .field-name-field-activity-logo .field-item').clone().insertBefore('.node-activity .photos .field-name-field-activity-ext-logos .field-items .field-item:first');
    
    jQuery(fieldItems).find('.field-item:first').addClass('active');
    
    
    jQuery('.node-activity .photos .field-name-field-activity-ext-logos .field-items .field-item').click(function(){
        
        jQuery('.node-activity .photos .photowall .field-item').replaceWith(jQuery(this).clone());
        jQuery(this).parent().find('.field-item').removeClass('active');
        jQuery(this).addClass('active');
    });
});
