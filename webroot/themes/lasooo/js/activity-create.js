jQuery(document).ready(function(){
    var field_activityTime = jQuery('#activity-node-form .activity-time input');
    
    field_activityTime.addClass('Wdate');
    field_activityTime.bind('focus', function(event){
        //alert('hello,world; ' + jQuery(this).attr('class'));
        WdatePicker({skin:'whyGreen',
                     dateFmt:'yyyy-MM-dd HH:mm', 
                     minDate:'%y-%M-{%d+1}'});
       
        
    });
});


