/*
Light Javascript Framework
Name Space: lf
verson: 0.2

Developed by Glools Team at 2010-10-26
*/

(function (){
    try{
        if (lf || window.lf) {
            alert("System error, base Namespace have been registered.");
            return true;
        }
    }catch(e){
        alert(e.message);
        return true;
    }

    var lf = {
        /*
            Global variables
        */
        'ns' : {
            '_vars' : {},
            '_plugins' : {},
            '_apps' : {},
            '_utilites' : {}
        },
        
        'getNS'        : function (c, n){
            //c: category, app, plugin, utilite, var
            //n: name path.
            var cN = c +'s';
            
            var ns = lf.getNameSpace(cN);
            
            if (typeof ns[n] != 'undefined') {
                return ns[n];
            }else{
                return ns;
            }
        },
        
        'setNS'        : function(c, n, v){
            
            if ('string' != typeof c || 'string' != typeof n) {
                return false;
            }
            
            var h = c + 'NameSpace';
            
            if ('function' == typeof lf[h] && lf[h][0]!='_') {
                return lf[h](n, v);
            }
            
            return false;
        },
        
        'getNameSpace' : function(n) {
            return lf.ns['_'+n];
        },
        
        
        'appNameSpace' : function(n, v) {
            return lf._nameSpace(n, 'apps', v);
        },
        
        'pluginNameSpace' : function(n, v) {
            return lf._nameSpace(n, 'plugins', v);
        },
        
        'utilityNameSpace' : function(n,v) {
            if ('object'!=typeof(v)) {
                return false;
            }
            lf._nameSpace(n, 'utilities', v);
        },
        
        'varsNameSpace' : function(n,v) {
            if ('string' != typeof(n)) {
                return 'undefined';
            }
            
            return lf._nameSpace(n, 'vars', v);
        },
        
        '_nameSpace' : function(n, p, v) {
            if ('string' != typeof(n)) {
                return false;
            }
            p = '_'+p;
            
            if (!lf.ns[p]) {
                alert('[Error by lf] Base name \"'+p+'\" does not allowed.');
                return false;
            }
            
            lf.ns[p][n] = v;
            return lf.ns[p][n];
        }
    }
    
    window.lf = lf;
    window.LF = lf;
    
})();


/*
    Load and run registered plugins after document loaded.

    note: hooks
    main: the method will be executed in plugin.
*/

jQuery(document).ready(function(){
    
    var plugins = lf.getNameSpace('plugins');
    
    
    for(var p in plugins) {
    
        var plugin = plugins[p];
        
        switch(typeof(plugin)) {
            case 'object':
                if(plugin.main && 'function' == typeof(plugin.main)){
                    plugin.main.apply(plugin);
                }
                
                break;

            case 'function':
                plugin.apply(plugin);
                break;
            default:
                alert(p+' is type '+typeof(plugin));
        }
        
    }
});
