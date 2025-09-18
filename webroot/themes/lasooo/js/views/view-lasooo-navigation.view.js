jQuery(document).ready(function(e){
    var $ = jQuery;    
    var baseSelector = '#region-sidebar_slideshow #view_lasooo_navigation';
    var baseTargetSelector = "#region-sidebar_slideshow"; //['id'!='view_lasooo_navigation']
    var activedBlock = [];    

    function addCloseButton(parentSelector){
        $(parentSelector).find('div.photoHandleButton').remove();
        
        $(parentSelector).append("<div class='photoHandleButton close'></div>");
        $(parentSelector+' .close').click(function(e){
            $(parentSelector).slideUp();
        });
    }
    
    /* menubar control */
    $(baseSelector+" .item").each(function(e){
        $(this).bind('click', function(e){
            var ids = $(this).attr('id').split(':');
            if (ids[1].length>0 && $(baseTargetSelector+" "+ ids[1]).length>0 && 'none'==$(baseTargetSelector+" "+ ids[1]).css('display')) {
                $(baseTargetSelector+">div[id!='view_lasooo_navigation']").slideUp();
                $(baseTargetSelector+" "+ ids[1]).slideDown();
                addCloseButton(baseTargetSelector+" "+ ids[1]);
                enlightButton(this);
            }else{
                $(baseTargetSelector+" "+ ids[1]).slideUp();
            }
            
        });
    });
    
    var adIds = new Array();
    
    $(baseTargetSelector+">div[id!='view_lasooo_navigation']").each(function(e){
        adIds.push($(this).attr('id'));
        
    });
    
    var currentADId = Math.ceil(Math.random()*(adIds.length-2));
    
    //alert(adIds.length +';'+currentADId+';'+adIds[currentADId]);
    if ($(baseTargetSelector+" > #"+adIds[currentADId]).length >0) {
        $(baseTargetSelector+" > #"+adIds[currentADId]).css('display', 'block');
    }
    

    /* block-title animatation */
    $(baseSelector+' .block-title').find('span').each(function(e){
        $(this).bind('mouseover', function(){
            $(this).addClass('itemActive');
        }).bind('mouseout', function(){
            $(this).removeClass('itemActive');
        });
    });

    $(baseSelector+' .block-content').find('.item').each(function(e){
        $(this).bind('mouseover', function(){
            $(this).addClass('itemActive');
        }).bind('mouseout', function(){
            $(this).removeClass('itemActive');
        });
    });
});
