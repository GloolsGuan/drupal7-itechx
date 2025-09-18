<?php
/**
 * Show deferent theme For special client device
 * 
 * @Author GloolsGuan@glools.com
 * 
 * 201401
 * 
 * Supported device:
 * - Personal computer, the default device
 * - Iphone
 * - Ipad
 * - Android
 * - Android Pad
 * */
 Lib_Glools_Load::loadFile('glools/useragent', 'lib');
/*
  Current client device 
**/
function glools_device_init(){
    global $conf;
    
    $userAgent = new Lib_Glools_Useragent();
    $client_info = $userAgent->getClientInfo();
    
    //debug_rdata(__FUNCTION__, $userAgent);
    debug_rdata(__FUNCTION__, $client_info);
    
    glools_device_setTheme($client_info);
}

/*
    Testing client device
**/
function _glools_device_testing(){
    $agentString = $_SERVER['HTTP_USER_AGENT'];
    
    $strAPosition = strpos($agentString, '(');
    $strBPosition = strpos($agentString, ')');
    $clientInfo = substr($agentString, $strAPosition+1, $strBPosition-$strAPosition-1);
    debug_rdata(__FUNCTION__, array($agentString, $clientInfo));
    
}

/*
    Set theme for client device
**/
function glools_device_setTheme($client_info){
    global $conf;
    //$conf['theme_default'] = '';
    
    $device_type = $client_info['d_type'];
    $device_type = ('pc'==$device_type) ? 'default' : $device_type;
    
    $themes = array(
        'mobile'  => 'bootstrap_business_mobile',
        'default' => 'bootstrap_business',
        'pad'     => 'bootstrap_business',
        'wxapp'   => 'bootstrap_business_mobile',
        'wxmedia' => 'bootstrap_business'
    );
    
    /*
    $themes = array(
        'mobile'  => 'lasooo_mobile',
        'default' => 'lasooo',
        'pad'     => 'lasooo',
        'wxapp'   => 'lasooo_mobile',
        'wxmedia' => 'lasooo'
    );
     */
    
    //$device_type = $client_info['d_type'];
    $theme = $themes[$device_type];
    $conf['runtime_theme'] = ['client'=>$client_info,'theme'=>$theme, 'theme_key'=>$device_type];
    //debug_rdata(__FUNCTION__, [$device_type, $theme, $client_info]);
    $conf['original_theme'] = 'bootstrap_business';
    
}
