jQuery(document).ready(function(){
    var $ = jQuery;
    var descSelector = $('#user-register-form .form-register-description');
    
    descSelector.find('.description').bind('click', function(){
        descSelector.find('div').slideToggle('slow', function(){
            //(descSelector.find('.description').html());
            if ('↓'==descSelector.find('.description').html()) {
                descSelector.find('.description').html('↑');
            }else{
                descSelector.find('.description').html('↓');
            }
            
        });
    });
    
    /* form-item description */
    
   var fiSelector = $('form#user-register-form .form-item');
   
   fiSelector.find('.form-text').bind('focus', function(){
       //alert($(fiSelector).find('.description').length);
       $(this).parent().find('.description').show('normal');
   }).bind('blur',function(){
       $(this).parent().find('.description').hide('normal');
   });
   
});
