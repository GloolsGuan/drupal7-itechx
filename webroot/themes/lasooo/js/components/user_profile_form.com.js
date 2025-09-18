jQuery(document).ready(function(){
    var $ = jQuery;
    // Main form selector
    var mS = $('#user-profile-form');
    // Navigator selector
    var nS = mS.find('.account-settings-navigation');
    
    nS.find('li').click(function(){
       var idArgs = $(this).attr('id').split(':');
       var clickItem = this;
       $(this).addClass('active');
       var items = mS.find('.form-wrapper');
       
       for(var i=0; i<items.length; i++) {
           //alert(idArgs[1] +':'+$(items[i]).attr('id'));
           
           if (idArgs[1]==$(items[i]).attr('id')) {
               $(items[i]).slideDown('normal');
               nS.find('li').removeClass('active');
               $(clickItem).addClass('active');
           }else{
               if (!$(items[i]).hasClass('form-actions')) {
                   $(items[i]).slideUp();
               }
               
               if ($(clickItem).attr('id')!=$(items[i]).attr('id')) {
                   $(items[i]).removeClass('active');
               }
           }
       }
    });
});
