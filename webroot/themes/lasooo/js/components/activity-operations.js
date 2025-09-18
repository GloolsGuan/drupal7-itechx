(function(){
    
    var $ = jQuery;
    
    
    
    var hlist = {
        
        main : function(){
            var aS =  $('.node.node-activity .actions').find('a[class~=ajax-invoke]');
            
            aS.bind('click', function(){
                var href = $(this).attr('href');
                hlist.ajaxInvoke(href, this);
                //alert(href);
                return false;
            });
        },
        
        ajaxInvoke: function(url, oE){
            $.ajax({
                'url'      : url,
                'dataType' : 'json',
                'success'  : function(response, status){
                    if ('string'==typeof response.action && hlist['action'+response.action]) {
                        hlist['action'+response.action](response.data, oE, response.status);
                    }else{
                        alert('Error, Action does not exist:' . response.action);
                    }
                }
            });
        },
        
        /*
            Actions handler
        */
        actionLock: function(data, oE, status){
            // Failed to execute the request
            if (0==status) {
                return false;
            }
            
            $(oE).text(data.text);
            $(oE).attr('href', data.url);
        },
        
        actionFavorite : function(data, oE, status){
            if (0==status) {
                return false;
            }
            
            $('#node-'+data.node).slideUp('normal');
        }
    };
    
    
    lf.setNS('plugin', 'glools.activity.hlist', hlist);
})();
