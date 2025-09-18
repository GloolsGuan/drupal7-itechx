<div id='field-<?php print $this->field_name;?>' class='field field-wrapper field-area-wrapper field-<?php print $this->field_name;?>'>
    <!-- <div class='field-header '>
        <h3><?php print $this->label;?></h3>
    </div>-->
    <div>
    <div class='field-body field-body-tabs'>
        <span class='tab-handler clicked' id='field-tab-handler'>城市地区选择</span>
        <span class='tab-list' id='field-tab-list'>已选择列表</span>
<!--        <span class='tab-map' id='field-tab-map'>地 图</span>-->
    </div>
    <div class='field-body field-body-content field-body-handler'>
        <div class='area-box-wrapper area-province-wrapper'></div>

        <div class="wrap-city">
            <label>市</label>
            <div class='area-box-wrapper area-city-wrapper'><span class="area-item area-item-all" id="area-all-city">所有市</span></div>

            <div class="wrap-town">
                <label>区</label>
                <div class='area-box-wrapper area-town-wrapper'><span class="area-item area-item-all" id="area-all-town">所有区</span></div>
            </div>
        </div>
    </div>
    <div class='field-body field-body-content field-body-list'></div>
    <div class='field-body field-body-content field-body-map'>This is a map.</div>
    <div class='field-footer'>
        <textarea class="hide field-areas" type='hidden' name='select_areas'>all</textarea>
    </div>
    </div>
</div>
<style>
#field-areas .wrap-city{ background:#f3f3f3;border:1px solid #dedede;padding:20px}
#field-areas .wrap-town{ background:#eaeaea;border:1px solid #dddddd;margin-top:20px;padding:20px}
    .field-area-wrapper .field-body-list,
    .field-area-wrapper .field-body-map{
        display:none;
    }
    .field-area-wrapper .field-body-tabs span{
        padding:10px 20px;
        margin:5px 20px;
        background:#999;
        font-size:1.2em;
        color:#FFF;
        display: inline-block;
        cursor:pointer;
    }
    .field-area-wrapper .field-body-tabs span.clicked{
        padding:10px 20px;
        margin:5px 20px;
        background:#0489B1;
        font-size:1.2em;
        color:#FFF;
        display: inline-block;
        cursor:pointer;
    }

    .field-area-wrapper .field-body-handler>div{
        margin-bottom:30px;
        border:1px #EEE dotted;
    }
    .field-area-wrapper .field-body-handler span{
        padding:6px 15px;
        margin:5px;
        background:#BBB;
        color:#FFF;
        display: inline-block;
        cursor:pointer;
    }
    .field-area-wrapper .field-body-handler span.hovering{
        background:#666;
    }

    /* Exists city, actived */
    .field-area-wrapper .field-body-handler span.actived{
        background:#086A87;
    }
    /* New city, clicked */
    .field-area-wrapper .field-body-handler span.clicked{
        background:#C8FE2E;
        color:#666;
    }
    /* Exists city, actived */
    .field-area-wrapper .field-body-handler span.current{
        background:#086A87;
        color:#FFF;
    }


    .field-area-wrapper .field-body-list .area-item{
        display:block;
        padding:10px 20px;
        margin: 5px;
    }
    .field-area-wrapper .field-body-list .area-list-level-1{
        width:100%;
        font-size:1.5em;
        margin-top:10px;
        border-bottom: 1px #999 solid;
    }
    .field-area-wrapper .field-body-list .area-list-level-2{
        /*margin-left:20px;*/
        font-size:1.2em;
        background:#EEE;

    }
    .field-area-wrapper .field-body-list .area-list-level-3{
        display:inline-block;
        padding:5px 10px;
        margin: 3px 5px;
    }
</style>
<script type='text/javascript'>

/**
 * TODO:
 * 1. envs.loaded.clicked, 只需要保存ID，其它不需要保存。
 *
 * @param {type} $
 * @returns {undefined}
 */
;(function($){

    <?php if(!empty($this->default_value) && is_string($this->default_value)):?>
    var selectedFromServer = <?php print json_encode(explode(';', $this->default_value));?>;
    <?php else:?>
    var selectedFromServer = [];
    <?php endif;?>
    console.log('selectedFromServer',selectedFromServer);



    var envs = {
        'selected' : [],
        'url_load' : "<?php print $this->url_load;?>",
        'loaded' : {
            'clicked' : {},
            'records': {},
            'selected' : []
            //'selected' : selectedFromServer.slice(0)
        },
        'se' : {
            'fieldWrapper' : 'div#field-<?php print $this->field_name;?>',
            'areaProvince' : '.area-province-wrapper',
            'areaCity' : '.area-city-wrapper',
            'areaTown' : '.area-town-wrapper',
            'tabs' : '.field-body-tabs'
        },
        'level' : {'0':'province', '1':'city', '2':'town'},
        'provinceLen':0,
        'cityLen':0,
        'town':0,
        'cityHtml':[],
         'townHtml':[]
    };
    //$.extend(envs.loaded.clicked,{620000:[620100,620200,620300,620400,620500,620600,620700,620800,620900,621000,621100,621200,622900,623000]})
    ;function changeArr(arr){
                var provinceArr = [],
                        cityArr = [],
                        townArr = [];

                //有多少省
                for(var i= 0,j = arr.length; i<j; i++){
                    if(arr[i].indexOf('/') === -1){
                        provinceArr.push(arr[i]);
                        delete arr[i]
                    }
                }
                //有多少市
                for(var i= 0,j = arr.length; i<j; i++){
                    if(arr[i] !== undefined && arr[i].split('/').length === 2){
                        cityArr.push( arr[i].split('/')[1]);
                        delete arr[i]
                    }
                }
                //有多少区
                for(var i= 0,j = arr.length; i<j; i++){
                    if(arr[i] !== undefined){
                        townArr.push(arr[i].split('/')[2]);
                    }
                }
                return [provinceArr,cityArr,townArr]
            };
         var newselectedFromServer = changeArr(selectedFromServer);
         console.log('newselectedFromServer',newselectedFromServer)

    var DbLite = {
        'SaveTo' : function(db, xPath, data){

            switch(db){
                case 'selected':
                    envs.loaded.selected.push(xPath);
                    break;
                case 'clicked':
                    var d = [];
                    //console.log(['DbLite.SaveTo testing - 1', data, data.length]);
                    if (data.length>0) {
                        for(var i in data) {
                            //console.log([i, data[i]]);
                            d.push(data[i].id);
                            //-- Saving data to records --
                            if (data[i] && !(data[i].id in envs.loaded.records)) {
                                envs.loaded.records[data[i].id] = data[i];
                            }
                        }
                    }
                    envs.loaded.clicked[xPath] = d;

                    break;
                default:
                    envs.loaded.records[xPath] = data;
            }

            return DbLite;
        },
        'LoadFrom' : function(db, xPath){
            var d = envs.loaded[db][xPath];
            //console.log(['LoadFrom', xPath, d]);
            return d;
        },
        'LoadRecords' : function(ids){
            var nodes = {};

            if (1>ids.length) {
                return false;
            }

            for(var i in ids) {
                nodes[ids[i]] = DbLite.LoadFrom('records', ids[i]);
            }

            return nodes;
        },


        /**
         * Load area records with Number-Index key.
         *
         * @param {type} level
         * @param {type} data
         * @returns {Boolean}
         */
        'LoadRecordsWithNI' : function(ids){
            var nodes = [];

            if (1>ids.length) {
                return false;
            }

            for(var i in ids) {
                nodes.push(DbLite.LoadFrom('records', ids[i]));
            }

            return nodes;
        },


        'RemoveFrom' : function(db, xPath){
            switch(db){
                case 'selected':
                    var ps = xPath.split('/');
                    var re = new RegExp('^' + xPath +'.*');

                    for(i in envs.loaded.selected) {
                        var p = envs.loaded.selected[i];
                        if (re.test(p)) {
                            envs.loaded.selected.splice(i, 1);
                        }
                    }

                    //console.log(['RemoveFrom', envs.loaded.selected]);

                    break;
                //-- Dont need to delete --
                default:
                    ;
            }

            return DbLite;
        },
        'SearchSelected' : function(xPath){
            var re = new RegExp('^' + xPath +'.*');
            var nIds = [];
            for(var i in envs.loaded.selected) {
                var p = envs.loaded.selected[i];
                if (re.test(p)) {
                    nIds.push(p);
                }
            }
            return nIds;
        },
        //-- Seeking path from lower to higher --
        'BuildX' : function(areaId, x){
            var area = DbLite.LoadFrom('records', areaId);
            var xPath = '';
            if (x && x.length>0) {
                xPath = [area.id, x].join('/');
            } else {
                xPath = area.id;
            }
            //console.log([area, areaId, xPath, x]);
            if ('0'===area.level) {
                return xPath;
            } else {
                return DbLite.BuildX(area.parent_id, xPath);
            }
        }
    };


    var AreaAnimation = {

            /**
             * Removing class "clicked","current";
             * RemoveRenderedAreas(clickedArea);
             * DbLite.RemoveFrom('selected', DbLite.BuildX(clickedArea.id));
             */
            'Animation_CancelActive' : function(clickedNode, clickedArea, clickedAreaId, clickedNodeId, clickedLevel){
                $(clickedNode).removeClass('clicked current');
                console.log(clickedArea.level);
                if ('2'!=clickedArea.level){
                 $('div.wrap-town').hide(); //影藏区
                  var $allCityBtn = $('div.area-city-wrapper').find('span:first');
                  $allCityBtn.is('.clicked') && $allCityBtn.removeClass('clicked');
                }
                console.log('0'==clickedArea.level)
                if('0'==clickedArea.level){

                    $('div.wrap-city').hide();
                }
                AreaAnimation.RemoveRenderedAreas(clickedAreaId);
                DbLite.RemoveFrom('selected', DbLite.BuildX(clickedArea.id));




            },

            'Animation_ChangeToCurrent' : function(clickedNode, clickedArea, clickedAreaId, clickedNodeId, clickedLevel){
                $(clickedNode).parent('.area-box-wrapper').find('span').removeClass('current');

                if ('2'!==clickedArea.level) {
                    $(clickedNode).addClass('current');
                }


                if ('2'==clickedArea.level){
                    console.log('点击区了');
                    $(clickedNode).addClass('current');

                    //return false;
                }

                AreaAnimation.DisplayAreas(clickedLevel, clickedAreaId);
            },

            /*
             $(node).addClass('clicked');
             if ('2'!==area.level) {
                 $(node).addClass('clicked current');
             }
             DbLite.SaveTo('selected', DbLite.BuildX(area.id));
             if ('2'==area.level){
                 return false;
             }
             LoadAreas(areaId, function(rsp){
                 //console.log(['OnClickedArea/LoadAreas testing', rsp.data]);
                 DbLite.SaveTo('clicked', areaId, rsp.data);
                 RenderAreas(level, rsp.data);
             }, function(rsp){
                 //console.log(['Failed', 'Failed to load areas by ' + areaId, area]);
             });*/
            'Animation_ActiveCurrent' : function(clickedNode, clickedArea, clickedAreaId, clickedNodeId, clickedLevel, cb,   pushHtmlNode){

                $(clickedNode).addClass('clicked');

                //-- "area" level--
                /*
                if ('2'!==clickedArea.level) {
                    $(clickedNode).parent('.area-box-wrapper').find('span').removeClass('current');
                    $(clickedNode).addClass('current');
                }
                */

                 $(clickedNode).parent('.area-box-wrapper').find('span').removeClass('current');
                 $(clickedNode).addClass('current');

                //console.log(['Animation_ActiveCurrent', clickedNode, clickedArea, clickedAreaId, clickedNodeId, clickedLevel]);
                //-- Saving current clicked node to DbLite.selected
                DbLite.SaveTo('selected', DbLite.BuildX(clickedArea.id));
                if ('2'==clickedArea.level){
                    //return false;
                }

                LoadAreas(clickedAreaId, function(rsp){
                    //console.log(['OnClickedArea/LoadAreas testing', rsp.data]);
                    DbLite.SaveTo('clicked', clickedAreaId, rsp.data);
                    //AreaAnimation.RenderAreas(clickedLevel, rsp.data);
                    //cb && cb(clickedNodeId)

                    if(cb){
                        AreaAnimation.RenderAreasJtj(clickedLevel, rsp.data,  pushHtmlNode);
                        cb && cb(clickedNodeId)
                    }else{
                        AreaAnimation.RenderAreas(clickedLevel, rsp.data);

                    }
                }, function(rsp){
                    //console.log(['Failed', 'Failed to load areas by ' + clickedAreaId, clickedArea]);
                });
            },
            /**
             * Render area list on specified level box
             *
             * 1. Empty current level box
             * 2. If current level is 1 "city", empty area level box
             *
             * Selector name rule of area level box：
             * .area-[level number]-wrapper
             *
             * @param {type} level "0", "1","2"
             * @param {type} ars, Area record list from database, with Number index
             * @returns {Boolean}
             * isFirseClick jtj 判断是否是首次绑定
             */
            'RenderAreas' : function(level, ars, isFirseClick){
                var allowedLevels = ['0','1','2'];
                if (!(level in allowedLevels)) {
                    //console.log(['Error area level', level]);
                    return false;
                }

                if (null===ars || 'undefined'===typeof(ars) || 1>ars.length) {
                    return false;
                }
                //每点都要获取当前点击区域的 父层DIV 如 省，市，区
                var levelClass = ['.area-',envs.level[level], '-wrapper'].join('');

                //-- Hide areas on province clicked --
                //console.log(['testing hide areas on province clicked', level]);

                if ('0'===level || '1'===level) {//当第一次或点省时 运行 - 每次都影藏区县
                    var HideAreaClass = ['.area-',envs.level['2'], '-wrapper'].join('');
                    //console.log(['testing hide areas on province clicked', level, HideAreaClass]);
                    $(envs.se.fieldWrapper).find(HideAreaClass).find('span').hide();
                    console.log('点省');
                    console.log('level',level);

                    $('div.wrap-town').hide();


                }

                if('2'===level){
                    console.log('点市');
                    isShowAllCityBtn(); //当点市时 是否显示 选中状态的 所有市 按钮
                    $('div.wrap-town').show();
                }
                console.log('level',level);

                //AddLoadedToRecords(data);
                var RemoveHides = [];
                for(var i in ars) {
                    var area = ars[i];
                    var id = '#area-item-'+area.id;
                    RemoveHides.push(id);
                    //console.log(['RenderAreas', area]);
                    if ('undefined'===typeof(area) || null===area) {
                        continue;
                    }

                    //-- Save loaded area to DbLite --
                    //envs.loaded[area.id] = area;
                    $(envs.se.fieldWrapper).find(levelClass).find('span').hide();

                    if (1>$(envs.se.fieldWrapper).find(levelClass).find(id).length) {
                        var buildElement = AreaAnimation.BuildElement(area);
                        //console.log(['Render areas', buildElement]);
                        $(envs.se.fieldWrapper).find(levelClass).append(buildElement);
                    }

                    $(envs.se.fieldWrapper).find(levelClass).find(RemoveHides.join(',')).show();
                }
                $(envs.se.fieldWrapper).find('span.area-item-all').show();
                if(isFirseClick != undefined)
                AreaAnimation.BindEvents('OnRenderedArea');
            },

            'RenderAreasJtj' : function(level, ars,   pushHtmlNode){
                var allowedLevels = ['0','1','2'];
                if (!(level in allowedLevels)) {
                    //console.log(['Error area level', level]);
                    return false;
                }

                if (null===ars || 'undefined'===typeof(ars) || 1>ars.length) {
                    return false;
                }
                //每点都要获取当前点击区域的 父层DIV 如 省，市，区
                var levelClass = ['.area-',envs.level[level], '-wrapper'].join('');

                //-- Hide areas on province clicked --
                //console.log(['testing hide areas on province clicked', level]);

                if ('0'===level || '1'===level) {//当第一次或点省时 运行 - 每次都影藏区县
                    var HideAreaClass = ['.area-',envs.level['2'], '-wrapper'].join('');
                    //console.log(['testing hide areas on province clicked', level, HideAreaClass]);
                    $(envs.se.fieldWrapper).find(HideAreaClass).find('span').hide();


                }

                //AddLoadedToRecords(data);
                var RemoveHides = [],jtjNum=0;
                for(var i in ars) {
                    ++jtjNum;
                    //console.log('jtjNum ='+jtjNum, 'ars.length = '+ars.length)
                    var area = ars[i];
                    var id = '#area-item-'+area.id;
                    RemoveHides.push(id);
                    //console.log(['RenderAreas', area]);
                    if ('undefined'===typeof(area) || null===area) {
                        continue;
                    }

                    //-- Save loaded area to DbLite --
                    //envs.loaded[area.id] = area;
                    $(envs.se.fieldWrapper).find(levelClass).find('span').hide();

                    if (1>$(envs.se.fieldWrapper).find(levelClass).find(id).length) {
                        var buildElement = AreaAnimation.BuildElement(area);
                        envs[pushHtmlNode].push(buildElement)
                        //console.log('envs[pushHtmlNode]='+envs[pushHtmlNode])
                        //console.log( envs[pushHtmlNode].length)
                        //console.log('levelClass='+levelClass)
                        //console.log('-----------------')


                        if(pushHtmlNode=='cityHtml'){
                            //console.log(envs.provinceLen +'==============='+ newselectedFromServer[0].length)
                            if(envs.provinceLen === newselectedFromServer[0].length-1 && jtjNum ===ars.length){
                                //console.log('levelClass2222='+levelClass)
                                $(envs.se.fieldWrapper).find(levelClass).append(envs[pushHtmlNode].join(''));
                            }
                        }else{
                           // console.log(envs.cityLen +'==============='+ newselectedFromServer[1].length)
                            if(envs.cityLen === (newselectedFromServer[1].length-1) && jtjNum ===ars.length){
                                //console.log('levelClass33333='+levelClass)
                                $(envs.se.fieldWrapper).find(levelClass).append(envs[pushHtmlNode].join(''));
                            }

                        }


                        //console.log(['Render areas', buildElement]);

                    }

                    $(envs.se.fieldWrapper).find(levelClass).find(RemoveHides.join(',')).show();
                }
                 $(envs.se.fieldWrapper).find('span.area-item-all').show();

            },

            'DisplayAreas' : function(clickedLevel, clickedAreaId){
                if (envs.loaded.clicked[clickedAreaId] && envs.loaded.clicked[clickedAreaId].length>0) {
                    var ids   = envs.loaded.clicked[clickedAreaId];
                    var areas = DbLite.LoadRecordsWithNI(ids);
                }

                AreaAnimation.RenderAreas(clickedLevel, areas);
            },

            'BindEvents' : function(eventTag){
               //console.log(['BindEvents...', eventTag]);
               if ('OnRenderedArea'==eventTag) {
                   var fap = new FieldAreaPlugin();
                   fap.EventOnClickingArea();
               }

            },

            /**
             * TODO: Whether the area is selected?
             * @param {type} area
             * @returns {field_areas_default.tpl_L3.FieldAreaPlugin.BuildElement.html}
             */
            'BuildElement' : function(area){
                //console.log(area);
                var html = '<span class="area-item" id="area-item-'+area.id+'">'+area.zone_name+'</span>';
                return html;
            },

            /**
             * Removing all rendered areas depend on aid, including children.
             * @param {type} aid
             * @returns {Boolean}
             */
            'RemoveRenderedAreas' : function(aid){
                var ras = DbLite.LoadFrom('clicked', aid);
                //console.log(['RemoveRenderedAreas - 2', aid, envs.loaded.clicked, ras]);

                var handlerAreas = $(envs.se.fieldWrapper).find('.field-body-handler span.area-item');

                if (!ras  || 1>ras.length) {
                    return true;
                }

                for (var i in ras) {
                    var areaId = ras[i];
                    var id = ['area', 'item', areaId].join('-');
                    //console.log(['RemoveRenderedAreas - 3', id]);
                    handlerAreas.remove('#'+id);
                    AreaAnimation.RemoveRenderedAreas(areaId);
                }
            }
        };

    /**
     * Global function: LoadSelected
     * Invoke from: $.gfield.areas.LoadSelected();
     */
    var LoadSelected = function(){
        return envs.loaded.selected.join(';');
    };



    /**
     * Global function: LoadAreas
     * INvoke from: $.gfield.areas.LoadAreas();
     * Load areas from server or internal DbLite
     *
     * @param {type} parentId
     * @param {type} onSuccess
     * @param {type} onFailed
     * @returns {Boolean}     */
    var LoadAreas = function(parentId, onSuccess, onFailed){

        if (envs.loaded.clicked[parentId] && envs.loaded.clicked[parentId].length>0) {
            var ids = envs.loaded.clicked[parentId];

            onSuccess({'status':'success','code':200, 'data':DbLite.LoadRecordsWithNI(ids)});
            return true;
        }

        $.post(envs.url_load, {'parent_id':parentId}, function(rsp, status, xhr){
            if ('success'==rsp.status) {
                onSuccess(rsp);
            } else {
                onFailed(rsp);
            }
        }, 'json');
    };



    var FieldAreaPlugin = function(o){

            var self = this;
            var options = o;

            /**
             * Event: OnClickArea
             * 1. Make sure current status, "clicked","current","none"
             * 2. If clicked, and without current, Just attach 'current' status.
             * 3. If clicked, with current, Remove "current","clicked" status, and remove all children.
             * 4. If none, Attaching 'clicked','current' status, and build children.
             * 5. If level is province and operation is remove, areas should be clean up too.
             * 6. If level is city and operation is clicked, render children but do not remove other areas.
             *
             * So by this way, There are some operation mark exists:
             * - ShowAreas, show areas
             * - ActiveCurrent, active current status
             * - CancelActive, Remove all children areas.
             *
             * On :
             * - Show areas, current clicked area will attach "active" mark.
             * - CancelActive, current clicked area will remove "active" mark.
             *   If triggered level is province, area should be empty too.
             * - Active current, attaching "active" mark to clicked area first.
             *   then, attaching "current" mark too, It is means that the operation area currently.
             * - Initialize areas field, selected areas will attached to envs.loaded.selected automatically.
             *
             * @param {type} node
             * @param {type} e
             * @returns {undefined|Boolean}
             */
            var OnClickedArea = function(e){
                var clickedNode = $(this).eq(0);
                var clickedAreaId = $(clickedNode).attr('id').split('-')[2];
                var clickedNodeId = $(clickedNode).attr('id');

                if (!envs.loaded.records[clickedAreaId]) {
                    //console.log('Error, there is no areaId loaded, what happend ?');
                    return false;
                }

                var clickedArea = envs.loaded.records[clickedAreaId];
                var clickedLevel = ('0'===clickedArea.level) ? '1' : '2';
                var operatingMark = '';

                //-- Guess operating intention --
                //-- Clicked current active area, It is means cancel active --
                if ($(clickedNode).hasClass('clicked') && $(clickedNode).hasClass('current')) {
                    operatingMark = 'CancelActive';
                } else if ($(clickedNode).hasClass('clicked')) {
                    //-- Clicked active area again, which is not current operation area, It is means exchange back--
                    operatingMark = 'ChangeToCurrent';
                } else {
                    //-- The first time clicked area --
                    operatingMark = 'ActiveCurrent';
                }

                var animationHook = 'Animation_' + operatingMark;
                if (!AreaAnimation[animationHook] || 'function'!==typeof(AreaAnimation[animationHook])) {
                    alert('系统错误，区域点击响应动作无对应支持方法。');
                    return false;
                }
                //console.log(['OnClickedArea', 'Areas ...', animationHook, operatingMark]);
                AreaAnimation[animationHook](clickedNode, clickedArea, clickedAreaId, clickedNodeId, clickedLevel);
                //console.log(envs);
            };


            var OnClickedAreaJtj = function($this, cb,  pushHtmlNode){
                        var clickedNode = $this.eq(0);
                        var clickedAreaId = $(clickedNode).attr('id').split('-')[2];
                        var clickedNodeId = $(clickedNode).attr('id');

                        if (!envs.loaded.records[clickedAreaId]) {
                            //console.log('Error, there is no areaId loaded, what happend ?');
                            return false;
                        }

                        var clickedArea = envs.loaded.records[clickedAreaId];
                        var clickedLevel = ('0'===clickedArea.level) ? '1' : '2';
                        var operatingMark = '';

                        //-- Guess operating intention --
                        //-- Clicked current active area, It is means cancel active --
                        if ($(clickedNode).hasClass('clicked') && $(clickedNode).hasClass('current')) {
                            operatingMark = 'CancelActive';
                        } else if ($(clickedNode).hasClass('clicked')) {
                            //-- Clicked active area again, which is not current operation area, It is means exchange back--
                            operatingMark = 'ChangeToCurrent';
                        } else {
                            //-- The first time clicked area --
                            operatingMark = 'ActiveCurrent';
                        }

                        var animationHook = 'Animation_' + operatingMark;
                        if (!AreaAnimation[animationHook] || 'function'!==typeof(AreaAnimation[animationHook])) {
                            alert('系统错误，区域点击响应动作无对应支持方法。');
                            return false;
                        }
                        //console.log(['OnClickedArea', 'Areas ...', animationHook, operatingMark]);
                        AreaAnimation[animationHook](clickedNode, clickedArea, clickedAreaId, clickedNodeId, clickedLevel, cb,  pushHtmlNode);
                         //console.log(envs);
                    };



            /**
             * Bind event to displaied areas
             * @returns {undefined}
             */
            self.EventOnClickingArea = function (){
                //$(envs.se.fieldWrapper).find('.field-body-handler span').unbind('click');
                //$(envs.se.fieldWrapper).find('.field-body-handler span').bind('click', OnClickedArea);
                $(envs.se.fieldWrapper).on('click','span',OnClickedArea)

                var province = newselectedFromServer[0]; //[130000,230000,140000]
                var city = newselectedFromServer[1]; //[130300,140500, 140400];
                var town = newselectedFromServer[2]; //[130304,140423, 140424,140525,140581];
                //console.log('envs = ',envs)
                //市加载完回调
                var cbCity = function (cityId){
                    ++envs.cityLen;
                    //console.log('cbCity() : envs.cityLen === city.length', envs.cityLen === city.length,  envs.cityLen, city.length)
                    if(envs.cityLen === city.length){



                        var showTownArr = envs.loaded.clicked[city[city.length-1]]
                        //console.log(showCityArr)
                        $('#field-areas').find('.area-town-wrapper').children('span').hide();
                        for(var i=0,j=showTownArr.length; i<j; i++){
                           // console.log('town , showTownArr[i],city[city.length-1] ',town,showTownArr[i],province[province.length-1]);
                            if((province[province.length-1]).toString().substring(0,2)  != (showTownArr[i]).toString().substring(0,2)){
                                break;
                            }
                            $('#field-areas').find('.area-town-wrapper').find('span#area-item-'+showTownArr[i]).show();
                        }

                         //区加载完点击区
                        for(var i=0,j=town.length; i<j; i++){
                            OnClickedAreaJtj($('#area-item-'+town[i]))
                        }
                         $(envs.se.fieldWrapper).find('span.area-item-all').show();

                       // console.log('envs = ',envs)
                    }
                }

                //省加载完回调
                var cbProvince = function (pronvinceId){
                    ++envs.provinceLen;
                    if(envs.provinceLen === province.length){




                        var showCityArr = envs.loaded.clicked[province[province.length-1]]
                        //console.log(showCityArr)
                        $('#field-areas').find('.area-city-wrapper').children('span').hide();
                        for(var i=0,j=showCityArr.length; i<j; i++){
                            $('#field-areas').find('.area-city-wrapper').find('span#area-item-'+showCityArr[i]).show();
                        }

                        //市加载完点击市
                        for(var i=0,j=city.length; i<j; i++){
                            OnClickedAreaJtj($('#area-item-'+city[i]), cbCity,'townHtml')
                        }

                    }
                }

                //点击省
                for(var i=0,j=province.length; i<j; i++){

                    //$('#area-item-'+province[i]).addClass('clicked');
                    //if(i == (j-1)){
                        OnClickedAreaJtj($('#area-item-'+province[i]), cbProvince,'cityHtml')
                   // }
                }


            };


            var EventShowAreaList = function(node, e){

                var selectedAreas = $(envs.se.fieldWrapper).find('.field-body-handler .clicked');
                var testNode = selectedAreas[0];
                var testId = $(testNode).attr('id').split('-')[2];

                if (1>envs.loaded.selected.length) {
                    $(envs.se.fieldWrapper).find('.field-body-list').html('<span class="empty">没有任何城市和地区已经选择。</span>');
                    return true;
                }

                var es = {}; divs = {}; spans = {};
                $(envs.se.fieldWrapper).find('.field-body-list').empty();
                for(var i in envs.loaded.selected) {
                    var xPath = envs.loaded.selected[i];
                    var aIds = xPath.split('/');
                    var pLength = aIds.length;
                    var lastId = aIds.pop();
                    area = DbLite.LoadFrom('records', lastId);

                    switch(pLength) {
                        case 1:
                            var spanText = '<span class="area-item area-list-level-1" id="area-item-'+area.id+'" title="'+xPath+'">'+area.zone_name+'</span>';
                            $(envs.se.fieldWrapper).find('.field-body-list').append(spanText);
                            break;
                        case 2:
                            var spanText = '<span class="area-item area-list-level-2" id="area-item-'+area.id+'" title="'+xPath+'">'+area.zone_name+'</span>';
                            $(envs.se.fieldWrapper).find('.field-body-list').find('#area-item-'+aIds[0]).after(spanText);
                            break;
                        case 3:
                            var spanText = '<span class="area-item area-list-level-3" id="area-item-'+area.id+'" title="'+xPath+'">'+area.zone_name+'</span>';
                            $(envs.se.fieldWrapper).find('.field-body-list').find('#area-item-'+aIds[1]).after(spanText);
                            break;
                    }
                }
            };



            var BuildListElementForCityArea = function(areaId){
                var area = GetLoaded('records', areaId);
                var elements = [];

                elements.push('<span class="area-item" id="area-item-'+area.id+'">'+area.zone_name+'</span>');

                if ('2'==area.level) {
                    return elements.join('');
                }

                if (!(area.id in envs.loaded.selected)) {
                    //console.log(['Error', 'area.id does not exists in envs.loaded.clicked, what happened???', 'BuildListElement']);
                    return elements.join('');
                }

                var childrenNodeIds = envs.loaded.selected[area.id];
                //-- There is no children element clicked --
                if (1>childrenNodeIds.length) {
                    return elements.join('');
                }

                var nodeElements = [];
                for(n in childrenNodeIds) {
                    var node = envs.loaded.records[n];
                    nodeElements.push('<span class="area-item" id="area-item-'+area.id+'">'+area.zone_name+'</span>');
                }

                var children = '<div class="area-items area-children-wrapper">' + nodeElements.join('') + '</div>';
            };


            /**
             * Initialize tab exchange clicking event
             *
             * @returns {undefined}
             */
            var InitializeTabEvents = function(){
                var seTabs = $(envs.se.fieldWrapper).find(envs.se.tabs);

                seTabs.find('span').bind('click',function(e){

                    var tabName = $(this).attr('id').split('-')[2];
                    var seContentTab = '.'+['field', 'body', tabName].join('-');

                    if ($(this).hasClass('clicked')) {
                        $(this).removeClass('clicked');
                        //console.log([seContentTab, $(envs.se.fieldWrapper).find(seContentTab).length]);
                        $(envs.se.fieldWrapper).find(seContentTab).slideUp('fast');
                        return;
                    }

                    seTabs.find('span').removeClass('clicked');
                    $(this).addClass('clicked');
                    $(envs.se.fieldWrapper).find('.field-body-content').slideUp('fast');
                    $(envs.se.fieldWrapper).find(seContentTab).slideDown('fast');

                    if ($(this).hasClass('tab-list')) {
                        EventShowAreaList(this, e);
                    }

                });
            };


            self.init = function(){
                //console.log('Initializing is working...');
                LoadAreas(0, function(rsp){
                    //-- Add intialize areas "provinces" to clicked --
                    DbLite.SaveTo('clicked', 0, rsp.data);
                    //-- Render areas on level 0 -- 增加 isFirstClick ＝ 1
                    AreaAnimation.RenderAreas(0, rsp.data, 1);
                    //-- Bind event "click" to areas --
                    //EventOnClickingArea();
                }, function(rsp){
                    //console.log(['failed', rsp]);
                });

                InitializeTabEvents();
            };
        };


    /**
     * Register global jQuery namespace...
     */
    $.extend({
        'gfield': {
            'areas':{
                'LoadSelected' : LoadSelected,
                'LoadAreas' : LoadAreas
            }
        }
    });

    //判断是否要显示 ”所有市“ 按钮的选中样式
        function isShowAllCityBtn(){
            var showLen = 0,  clickedLen= 0, $allCityBtn =$('div.area-city-wrapper').find('span:first') ;

            $('div.area-city-wrapper').find('span').each(function(index){

                if($(this).css('display') != "none" && index != 0){
                       ++showLen;
                       if($(this).is('.clicked')){
                            ++clickedLen;
                       }
                }
            });
            console.log('showLen=',showLen,'clickedLen=',clickedLen);
            if(showLen == clickedLen){
                !$allCityBtn.is('.clicked') && $allCityBtn.addClass('clicked');
            }else{
                $allCityBtn.is('.clicked') && $allCityBtn.removeClass('clicked');
            }

        };



    //console.log($.gfield.areas);

    $(document).ready(function(){
        var objFieldArea = new FieldAreaPlugin({});
        objFieldArea.init();


        //点所有市 显示影藏当前所有市
                $('#area-all-city').on('click',function(){
                    var $this = $(this);

                    if($this.is('.clicked')){
                        $this.removeClass('clicked');
                        $this.parent().find('span').each(function(index){
                            if($(this).css('display') != "none" && index != 0 && $(this).hasClass('clicked')){
                                        if($(this).is('.current')){
                                            $(this).click();
                                        }else{
                                            $(this).click().click();
                                        }
                            }
                        })
                    }else{
                        $this.addClass('clicked');
                        $this.parent().find('span').each(function(index){
                            if($(this).css('display') != "none" && index != 0 && !$(this).hasClass('clicked')){
                                            $(this).click();
                                            console.log('clicked num')
                            }
                        });
                        $this.addClass('current').siblings().removeClass('current');
                        setTimeout(function(){$('div.wrap-town').hide()},800);
                    }
                });

                //点所有区 显示影藏当前所有区
                $('#area-all-town').on('click',function(){
                    var $this = $(this);

                    if($this.is('.clicked')){
                        $this.removeClass('clicked');
                        $this.parent().find('span').each(function(index){
                            if($(this).css('display') != "none" && index != 0 && $(this).hasClass('clicked')){
                                        if($(this).is('.current')){
                                            $(this).click();
                                        }else{
                                            $(this).click().click();
                                        }
                            }
                        })
                    }else{
                        $this.addClass('clicked');
                        $this.parent().find('span').each(function(index){
                            if($(this).css('display') != "none" && index != 0 && !$(this).hasClass('clicked')){
                                            $(this).click();
                                            console.log('clicked num')
                            }
                        });
                        $this.addClass('current').siblings().removeClass('current');
                        //setTimeout(function(){$('div.wrap-town').hide()},800);
                    }
                });

    });


})(jQuery);
</script>
