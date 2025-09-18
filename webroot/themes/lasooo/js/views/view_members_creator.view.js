jQuery(document).ready(function(e){
    $ = jQuery;
    $('#view_members_creator').bind('click', function(e){
        
        if (30==$(this).attr('clientHeight')) {
            $(this).find('.block-content>div.summary').hide();
            //$(this).addClass('view_members_creator-active');
            //alert('from top:'+$(this).attr('clientHeight'));
            for (var i=0; i<121; i++) {
                $(this).css('height', i);
            }
        }else{
            //alert('from second:'+$(this).attr('clientHeight'));
            //$(this).removeClass('view_members_creator-active');
            for (var i=120; i>29; i--) {
                $(this).css('height', i);
            }
            $(this).find('.block-content>div.summary').show();
        }
        
    });
});
