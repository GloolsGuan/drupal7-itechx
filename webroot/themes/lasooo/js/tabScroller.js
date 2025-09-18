/*
    Project: Glools-Drupal-JSLib-TabScroller
    Date: 2010-12-16
    Author: GloolsGuan@glools.com
    version: 0.6
        # Updated by GloolsGuan@glools.com on 2014-1-19
        --------------------------------------------------------------------------
        1. [debug:fixed] Checking whether "tabsMap" has children elements, If yes, Just add additional class.
           Otherwise 'Numbers' will be the children elements.
           

        # Updated by Glools Guan on 2013-02
	--------------------------------------------------------------------------
        1. [new] Support tab maps navigator. When you click one of the tab maps elements, The slide will scroll to the clicked tab.


	# Updated by Glools Guan on 2012-01-04
	--------------------------------------------------------------------------
	1. [debug:fixed] Clear the concept of "moveType", and fixed some bugs about multiple "moveType" concept applicating.
	2. [new] Add "autoScroll" property for set wether the tabs should scroll automatically.
        3. [debug:fixed] Fixed "nature" move type can not scroll circlly.
	4. [optimizing] Set "nature" as the default move type of automove function.
	5. [optimizing] Build "goByMovingPath" function for fetching all move type path.
	

    basic knowlage:
      region, the container of content region.It is a part of layout.
      content, the concreteness content of content region.
      scroll container, It is a sub content container of content in region, 
      It is used for plaing the scrolling animation.
	
*/
(function($){
    $.tabScroller = $.fn.tabScroller = function(options){
        var self = this;
       
        /*
            Options map
        */
        self.o = {
            'container'  : '.slideContainer',
            'content'    : '.slideContent',
            'tabsMap'    : '.tabsMap',
            'handlers'   : '.handlers',
            // default move content to left
            'bA'         : '.handlers .bA',
            // default move content to right
            'bB'         : '.handlers .bB',
            'tabIntTime' : 5000,
            'aniMoveTime': 2, 
            // allowed animations
            'animations' : ['flow', 'nature', 'shadow'],
            // current animation
            'animation'  : 'nature',
            // test step range, we will try to get bigest common divisor under testRange
            'testRange'  : 10,
            // the longest move time, second
            'longestTime': 2,
            //Tab classes for focus and blur
            'tabsClass'  : {'focus':'.focus', 'blur':'.blur'},
            // Buttons class for button animation
            'bsClass'    : {
                'mouseover' : '.mouseover',
                'mousedown' : '.mousedown',
                'mouseup'   : '.mouseup'
            },
            'events'     : {
                'tabsMap' : 'mouseover|mouseout',
                'bs'      : 'click',
                'content' : 'mouseover|mouseout'
            },
            // hooks
            'beforeMove'  : null,
            'afterMove'   : null,
            // auto scrolling
            'autoScroll'  : 'yes'
        }
        
        /*
            Running data
        */
        self.r = {
            'selectors'     : 'container,content,handlers,tabsMap,bA,bB',
            //resources, it is used to describe a global pointer
            'rs'            : {},
            // message
            'mes'           : [],
            // current position index
            'cPI'           : 0,
            // next position index
            'nPI'           : 0,
            // tab positions
            'tPs'           : [],
	        // scroll content DOM object
	        'sC'            : '',
	        // step range
	        'sR'            : [],
	        // original tabs number
	        'tabs'          : 0,
	        // runtime tabs value is double tabs number
	        'runtimeTabs'   : 0,
	        //cyclic point
	        'cyclicPoint'   : 0,
	        // last move time
	        'lastMoveTime'  : 0,
	        'cacheMovePath' : {},
            'autoScroll'    : ''
        }
        //alert('It is working');
        self.initOptions = function(options){
            
            var op = options, o=self.o;
			
	        for(a in o){
	            if(self.r.selectors.indexOf(a)>=0){
	                if(!op || !op[a] || 1>self.find(op[a]).length){
	                    
	                    if(1>self.find(o[a]).length){
	                        self.r.mes.push('Error, selector '+a+' is invalid.');
	                    }else{
	                        self.r[a] = self.find(o[a]);
	                    }
	                }else{
	                    self.r[a] = self.find(op[a]);
	                }
	            }else{
					
	                if(!op || !op[a]){
	                    self.r[a] = o[a];
	                }else{
	                    self.r[a] = op[a];
	                }
	            }
	            
	        }
            
	        // Scroll content must be child element of scroll container.
	        if(self.r.container[0] != self.r.content.parent()[0]){
		        self.r.mes.push("Scroll content did not found.");
	        }
	        
	        // Compute original tabs
	        self.r.tabs = Math.ceil(self.r.content.children(':first').attr('clientWidth')*self.r.content.children().length/self.r.container.attr('clientWidth'));
            
	        if(self.r.mes.length>0){
	            return false;
	        }
	        
	        return true;
        }
         
        self.initTabPositions = function(){
            if(1>self.r.runtimeTabs){
                self.r.mes.push('Runtime scroll tabs number is empty, '+ 
                                 'initContent function should be run before initTabPositions.');
                return false;
            }
            var t=0, cTW = self.r.container.attr('clientWidth'), cOW = self.r.content.attr('clientWidth');
            
            for(var i=0; t+cTW<cOW; i++){
                self.r.tPs.push(t = cTW*i);
            }
            return self;
        }
        
        self.initStepRange = function(){
        
            var w = self.r.container.attr('clientWidth');
            var t = [];
            for(var i=1;i<10;i++){
                var remainder = w%i;
                if(0 == remainder){
                    self.r.sR.push(i);
                }
            }
            
        }
         
        self.initContent = function(){
	        if (1>self.r.tabs) {
	            self.r.mes.push("Error, initialize tab positions should be done before initialize content.");
	            return false;
	        }
	        // Running scroll content, it elements are double of original content
	        //Updated by Glools for scrolling manually requirement in 20120801
            self.r.oC = self.r.content.clone();
	        if ('yes'==self.r.autoScroll) {
	            self.r.content.children().clone().appendTo(self.r.content);
	        }
	        
	        var eW = self.ceWidth(self.r.content.children(':first')[0]);
	        var cWidth = self.r.content.children().length * eW;
	        //var cWidth = self.r.content.children().length * self.r.content.children(':first').width();
	        
	        self.r.content.css('width', cWidth);
	        self.r.content.children().css('float', 'left');
	        self.r.runtimeTabs = self.r.content.children().length;
	        return self;
        }
        
        //Child element width
        self.ceWidth = function(e){
            var reNumber = /[0-9]+/gi;
            var ml = Number($(e).css('margin-left').match(reNumber));
            var mr = Number($(e).css('margin-right').match(reNumber));
            var bl = Number($(e).css('border-left-width').match(reNumber));
            var br = Number($(e).css('border-right-width').match(reNumber));
            var w = $(e).attr('clientWidth');
            
            w = w + ml + mr + bl + br;
            
            return w;
        }

        self.initTabsMap = function(){
            if(!self.r.tabsMap || 1>self.r.tabsMap.length){
                return false;
            }
            
            var t = [];
            
            for(var i=0; i<self.r.tabs; i++){
                if (self.r.tabsMap.children(":eq("+i+")").html()) {
                    self.r.tabsMap.children(":eq("+i+")").addClass('tabsMap_item tabsMap_item_' + i);
                }else{
                    t.push("<div class='tabsMap_item tabsMap_item_"+i+"'>"+i+"</div>");
                }
                //
            }
            
            if (t.length>0) {
                self.r.tabsMap.append(t.join(''));
            }
            
        }
	
        self.init = function(ops){
            
            self.initOptions(ops);
            self.initStepRange();
            self.initContent();
            self.initTabPositions();
            self.initTabsMap();
            setCurrentTab(0);
            setAvialableHandler(0);
            
            //Added by Glools at 20130426
            setHandlerForTabs();
            //bindEventHandlers();
            autoMove();
        }
        
        function bindEvents(){
            bindEventHandlers();
        }
        
        function removeEvents(){
            unbindEventAutoMove();
        }
        
        function bindEventAutoMove(){
            self.bind('onload', autoMove);
        }
        
        function unbindEventAutoMove(){
            self.unbind('mouseover', autoMove);
        }
        
        function bindEventHandlers(){
            //$('.mes').html('Bind event handlers.');
            self.r.bA.bind('click', function(){
                //Move to next tab as the nature move format.
                handleMove('left', 'nature');
            });
            self.r.bB.bind('click', function(){
                //Move to next tab as the nature move format.
                handleMove('right', 'nature');
            });
            // After once move, readd the autoMove event.
            self.r.handlers.bind('mouseover', sleepAutoMove);
            self.r.handlers.bind('mouseout', autoMove);
        }
        
        function unbindEventHandlers(){
            self.r.bA.unbind('click');
            self.r.bB.unbind('click');
        }
        
        function setHandlerForTabs(){
            //alert(self.r.tPs);
            $(self.r.tabsMap).find('.tabsMap_item').each(function(){
                $(this).click(function(){
                    var index = $(this).index();
                    //alert(self.r.tPs[index]);
                    self.goByMovingPath(index, 'nature', 50);
                });
            });
        }
        
        function handleMove(di, moveType, nPI){
            move(di, moveType);
        }
        
        //circulated move
        function autoMove(e){
            /* Added by Glools on 2011-12-19 for enable or disable autoMove */
			
            if ('yes'!=self.r.autoScroll) {
                return false;
            }
            
            self.r.container.bind('mouseover', sleepAutoMove);
            if ('number' == typeof(self.r.rs.auto)) {
                return true;
            }
            
            self.r.rs.isAutoMoveSleeping = false;
            
            self.r.rs.auto = setInterval(function(){
                move('left', 'nature');
            }, self.r.tabIntTime);
        }
        

        function sleepAutoMove(){
            var i=0, d = new Date();
            if (!self.r.rs.isAutoMoveSleeping) {
                self.r.rs.isAutoMoveSleeping = false;
            }
            if (self.r.rs.isAutoMoveSleeping) {
                //$('.mes').html('Stoped, and can not be stop again.');
                return true;
            }
            // clear auto move resource
            if ('number' == typeof(self.r.rs.auto)) {
                clearInterval(self.r.rs.auto);
                self.r.rs.auto = null;
                self.r.container.unbind('mouseover', sleepAutoMove);
                self.r.rs.isAutoMoveSleeping = true;
            }
            
            self.r.container.bind('mouseout', function(){
                if ('number'!=typeof(self.r.rs.auto) || 3000<(d.getTime()-self.r.lastMoveTime)) {
                    autoMove();
                }
                return false;
            });
            
        }
        
        // Move content to next tab
        function move(di, moveType, nPI){
            var f = self.r.animation.substr(0, 1).toUpperCase()+self.r.animation.substr(1);
            var d = new Date();
            var intervalTime = ('flow'!=moveType) ? 50 : 10 ;
            self.r.lastMoveTime = d.getTime();
            
            // call beforeMoveHook
            if ('function' == typeof(self.r.beforeMove)) {
                self.r.beforeMove.call(self);
            }
            
            // Added by Glools Guan on 2011-12-19
            if (!nPI) {
                nPI = getNextPositionIndex(di);
            }
            
            // Before moving, unbind all handlers event hook.
            unbindEventHandlers();
            
            // Scroll by moving path
            self.goByMovingPath(nPI, moveType, intervalTime);
            
            if ('function' == typeof(self.r.afterMove)) {
                self.r.afterMove.call(self);
            }
        }

        /*
            - TODO: by Glools Guan on 2011-12-20
              Because of double "setInterval" is executing, So one would effect another. It is better change other loop 
              method but not "setInterval"
        */
        self.goByMovingPath = function(nPI, moveType, intervalTime) {
            
            var mP = self.newMovingPath(nPI, moveType);
            
            var ii = setInterval(function(){
                if (1>mP.length) {
                    clearInterval(ii);
                    self.r.cPI = nPI;
                    
                    // Execute internal afterMove event.
                    eAfterMove(moveType);

                    // call beforeMoveHook
                    //if ('function' == typeof(self.r.afterMove)) {
                    //    self.r.afterMove.call(self);
                    //}
                    return true;
                }
                self.r.container.attr('scrollLeft', mP.shift());
            }, intervalTime);
        }
        
        function eAfterMove(moveType){
            
            if (self.r.cPI === self.r.tabs) {
                resetScrollRuntimeData();
            } else {    
                setCurrentTab(self.r.cPI);  
            }
            setAvialableHandler(self.r.cPI);
            return self;
        }

        /*
            - Updated by Glools Guan on 2011-12-18
              Add method "this.newMovePath" with two sub methods "this._movePath_cyclic" and "this.movePath_nature".
        */
        self.newMovingPath = function (nPI, moveType) {
            var movePathHook = '_movePath_'+moveType;
            if ( 'function' !=  typeof(this[movePathHook])) {
                if (true == this.debug) {
                    alert('Error, There is no valid movePath matched to moveType ('+moveType+').');
                }
            }
            return self[movePathHook].call(this, nPI, moveType);
        }

        /*
            - Added by Glools Guan on 2011-12-19
            Retrieve moving path with the same distance
        */
        self._movePath_flow = function (nPI, moveType) {
            if ('number' != typeof(nPI)) {
                return false;
            }
            
            var r = self.r, o=self.o;
            
            var c=r.tPs[r.cPI], n=r.tPs[nPI], mP =[], fraction=5, p=0, rM=0, lP=r.tPs[r.cPI],
                ct = r.container.attr('clientWidth'), co=r.content.children(':first').attr('clientWidth')*r.oC.children().length,
                d = Math.abs(c-n),
                di = c>n? 'right' : 'left';
            
            while (d>0) {
                lP = ('left'==di) ? lP+5 : lP-5;
                //if('cyclic' == moveType && nPI==self.r.tabs && lP==co){
                if (nPI==self.r.tabs && lP==co) {
                    mP.push(0);
                    break;
                }
                
                d = d-5;
                mP.push(lP);
            }
            return mP;
        }

        self._movePath_nature = function(nPI, moveType){
            if ('number'!=typeof(nPI)) {
                return false;
            }
            
            if (self.r.cacheMovePath[nPI]) {
                //return self.r.cacheMovePath[nPI];
            } else {
                self.r.cacheMovePath[nPI] = [];
            }
            
            /*
                c: current position, n: next position, rM: real move distance, lP: last position, mP: move path
                d: real distance from position A to B, di: direction(which direction does the content move forward to)
            */
            var r = self.r, o=self.o;
            
            var c=r.tPs[r.cPI], n=r.tPs[nPI], fraction=5, p=0, rM=0, lP=r.tPs[r.cPI],
                w = r.container.attr('clientWidth'),
                d = Math.abs(c-n),
                di = c>n? 'right' : 'left',
                co=r.content.children(':first').attr('clientWidth')*r.oC.children().length;
            
            // Now we compute the move path
            while (n != lP) {
                if (nPI==self.r.tabs && lP==co) {
                    self.r.cacheMovePath[nPI].push(0);
                    break;
                }
                rM = Math.ceil(d/fraction);
                var lP = ('left' == di)? lP+rM : lP-rM ;
                self.r.cacheMovePath[nPI].push(lP);
                d = d-rM;
            }
            
            return self.r.cacheMovePath[nPI];
        }
        
        //Get next tab index
        function getNextPositionIndex(di) {
            switch(di){
                case 'left':
                    return (self.r.cPI+1<self.r.runtimeTabs) ? self.r.cPI+1 : self.r.cPI;
                    break;
                case 'right':
                    return (self.r.cPI>0)? self.r.cPI-1 : self.r.cPI;
                    break;
                default:
                    return self.r.cPI;
            }
        }
        
        //for auto scroll, if current tab number is real tabs +1, then reset scroll. 
        function resetScrollRuntimeData() {
            
            self.r.cPI=0;
           
            if(!self.r.tabsMap || 1>self.r.tabsMap.length){
                return true;
            }
            setCurrentTab(0);
            
            return self;
        }
        
        function setCurrentTab(cPI) {
            var tabClass = '.tabsMap_item_'+cPI;
            if(!self.r.tabsMap || 1>self.r.tabsMap.length){
                return true;
            }
            
            self.r.tabsMap.children().removeClass('tabActive');
            self.r.tabsMap.find(tabClass).addClass('tabActive');
        }
        
        function setAvialableHandler(cPI){
            unbindEventHandlers();
            if (0==cPI) {
                if (!self.r.bA.hasClass('handlerActive')) {
                    self.r.bA.addClass('handlerActive');
                }
                if (self.r.bB.hasClass('handlerActive')) {
                    self.r.bB.removeClass('handlerActive');
                }
                self.r.bA.bind('click', function(){
                    //Move to next tab as the nature move format.
                    handleMove('left', 'nature');
                });

            } else if (cPI >= self.r.tabs-1) {
                if (self.r.bA.hasClass('handlerActive')) {
                    self.r.bA.removeClass('handlerActive');
                }
                if (!self.r.bB.hasClass('handlerActive')) {
                    self.r.bB.addClass('handlerActive');
                }
                self.r.bB.bind('click', function(){
                    //Move to next tab as the nature move format.
                    handleMove('right', 'nature');
                });
            } else {
                if (!self.r.bA.hasClass('handlerActive')) {
                    self.r.bA.addClass('handlerActive');
                }
                if (!self.r.bB.hasClass('handlerActive')) {
                    self.r.bB.addClass('handlerActive');
                }
                bindEventHandlers();
            }
            return self;
        }

        function isBoundary(di){
            if ('left'==di) {
                return self.r.cPI==self.r.tabs-1;
            } else {
                return 0==self.r.cPI;
            }
        }
	    // Initialize process
        self.init(options);
    }
}(jQuery));

/*
// -- Usage sample --
jQuery(document).ready(function(){
    //alert(jQuery.browser);
    var options = {
        'autoScroll' : 'yes' // or 'no'
    }
	setTimeout(function(){
		jQuery('#block-runfa-slide').tabScroller(options);
	}, 10000);
	
});
*/
