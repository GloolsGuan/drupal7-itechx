<?php


function frontend_proprocess_page_build(&$vars){
    $path = request_path();
    frontend_load_preset_Styles($vars, $path);
    frontend_load_preset_Javascripts($vars, $path);
    
    if (_frontend_checkRequest($vars)) {
       frontend_load_presetView($vars, $path);
    }
     
    $channel = frontend_get_channel($vars);
    Glools_Inc_Tools::loadInc($channel, 'channel', 'frontend');
    $hook = 'frontend_channel_' . $channel . '_page_build';
    //var_dump($hook);
    if (function_exists($hook)) {
        $hook($vars);
    }
}


function frontend_node_viewModeStyle(&$node){
    $viewMode = $node['#view_mode'];
    $nodeType = $node['#node']->type;
    $styleFile = path_to_theme() . '/css/nodeviews/' .$nodeType . '-' . $viewMode . '.node.css';
    $styleDir  = DRUPAL_ROOT .'/' . $styleFile;
    
    if (file_exists($styleDir)) {
        drupal_add_css($styleFile);
    }
    
    $node['#attributes'] = array(
        'class' => array($nodeType . '-' . $viewMode)
    );
}

function frontend_get_channel(&$vars){
	static $channel;
    if (!empty($channel)) {
    	return $channel;
    }
    
    $path = request_path();
    
    preg_match_all('/^[a-zA-Z\-]+/', $path, $matched);
    
    if (empty($path)) {
        $channel = 'frontpage';
    }else{
        $channel = $matched[0][0];
    }
    return $channel;
}

function frontend_load_preset_Styles(&$vars, $path){
	$channel = frontend_get_channel($vars);
    $hook = 'frontend_preset_styles_' . $channel;
    
    Glools_Inc_Tools::loadInc('style-preset', 'lib', 'frontend');
    
    if (function_exists($hook)) {
    	$hook($vars, $path);
    }
}

function frontend_load_preset_Javascripts(&$vars, $path){
    $channel = frontend_get_channel($vars);
    
    $hook = 'frontend_preset_js_' . $channel;
    
    Glools_Inc_Tools::loadInc('js-preset', 'lib', 'frontend');
    
    if (function_exists($hook)) {
        $hook($vars, $path);
    }
}

/**
 * Prepare preseted view data for channels block which defined in views-preset.data.inc
 *
 */
function frontend_load_presetView(&$vars, $requestPath){
    
    $channel = frontend_get_channel($vars);
    
    $hook = 'frontend_channel_' . $channel . '_viewPreset';
    
    //Glools_Inc_Tools::loadInc('view-preset', 'lib', 'frontend');
    Glools_Inc_Tools::loadInc($channel, 'channel', 'frontend');
    
    if (function_exists($hook)) {
        $viewData = $hook($vars, $requestPath);
        
        //-- Invoke "MODULE_gview" hook for alter view data --
        drupal_alter('gview', $viewData);
        
        //
        //glools_load_views($viewData->section, $vars, $viewData->data, 'frontend');
    }
}


function frontend_load_viewsPreset($section){
    static $views;
    
    if (!file_exists(dirname(__FILE__).'/../views-preset.data.inc')) {
        return null;
    }
    
    $viewsPreset = include(dirname(__FILE__).'/../views-preset.data.inc');
    
    if (isset($viewsPreset[$section])) {
        return $viewsPreset[$section];
    }
    
    return null;
}

function frontend_load_viewsPresetData($view, $region, $weight, $section){
	$viewSuffix = str_replace('/', '_', $view['view']);
    $dataFile = $viewSuffix . '.data.inc';
    
    Glools_Inc_Tools::loadInc($viewSuffix, 'data', 'frontend');
    
    $hooks = array();
    
    if (isset($view['data hook suffix'])) {
    	$hooks[] = $hooks[0] . trim(str_replace(' ', '_', $view['data hook suffix']));
    }
    
    $hooks[] = 'frontend_preset_viewData_'.$viewSuffix;
    
    $data = null;
    foreach ($hooks as $hook) {
    	if (function_exists($hook)) {
    		$data = $hook($view, $region, $weight, $section);
            break;
    	}else{
    		debug_record_data(__FUNCTION__.':function does not exists.', $hook);
    	}
    }
    return null;
}

/**
 * Description:
 *   Frontend module only work for renderring general frontend page, It is not work for personal center,
 * admin panel, or node edit panel.
 * */

function _frontend_checkRequest(&$vars) {
    if ( ! _frontend_checkRequest_isBlocked($vars)
        && (_frontend_checkRequest_isMenu($vars)
        || _frontend_checkRequest_isNode($vars)
        || _frontend_checkRequest_isSpecialtopic($vars) ))
    {
        return true;
    }
    
    return false;
}

function _frontend_checkRequest_isMenu(&$vars){
    static $channelMenus;
    
    $requestPath = request_path();
    
    $queried = db_query('select a.*,b.source,b.alias  from {menu_links} as a left join {url_alias} as b on (a.link_path=b.source) where menu_name= :menu ', array(':menu'=>'main-menu'));
    
    if (1>$queried->rowCount()) {
        return false;
    }
    
    $channelMenus = $queried->fetchAllAssoc('mlid');
    
    $valid = false;
    
    foreach($channelMenus as $menu) {
        if($requestPath==$menu->alias || $requestPath==$menu->source) {
            $valid=true;
            break;
        }
    }
    
    return $valid;
}

function _frontend_checkRequest_isNode(&$vars){
    $path = drupal_get_normal_path(request_path());
    
    if (preg_match('/^node\/[0-9]+$/', $path)) {
        return true;
    }
    
    return false;
}

function _frontend_checkRequest_isSpecialtopic(&$vars){
    return true;
}

function _frontend_checkRequest_isBlocked(&$vars){
    static $blockedPath ;
    $blockedPath = array(
        '/^user.*/',
        '/^batch.*/'
    );
    
    $path = drupal_get_normal_path(request_path());
    
    foreach ($blockedPath as $pathPreg ) {
        
        if (preg_match($pathPreg, $path)) {
            return true;
            break;
        }
    }
    
    return false;
}
