<?php
/*
 * Created on 7 Dec, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
/**
 * 
 * Description:
 * The class base on 'glools/loadinc' library  
 */
class Glools_Inc_Tools{
    
    static public $objLibLoadInc = array();
    
    // Only save the last module you set.
    static public $module = null;
    
    static public function loadInc($name, $type, $module){
        if(null !== $module){
            self::setModule($module);
        }
        //echo $name . '; ' . $type . '; ' . $module . '<br />';
        self::init($module)->load($name, $type);
    }
    
    static public function loadData($name, $type, $module) {
    	if(null !== $module){
            self::setModule($module);
        }
        
        return self::init($module)->load($name, $type);
    }
    
    
    static public function renderTpl($vars, $name, $module){
        if (null !== $module) {
        	self::setModule($module);
        }
        
        /*
        $template = drupal_get_path('module', $module) . "/inc_{$typeInfo['path_suffix']}/{$name}.{$typeInfo['file_suffix']}.inc";
        if (!file_exists($template)) {
        	return null;
        }
        */
        return self::init($module)->loadTpl($name, $vars);
    }
    
    static public function getIncPath($name, $type, $module) {
        if(null !== $module){
            self::setModule($module);
        }
        return self::init($module)->getPath($name, $type);
    }
    
    static public function setModule($module) {
        if( !module_exists($module) ){
            //throw new Exception("Error, Module '$module' does not exist.");
        }
        
        if( !isset(self::$objLibLoadInc[$module]) ){
            self::$objLibLoadInc[$module] = null;
        }
    }
    
    static protected function init($module) {
        if( null !== self::$objLibLoadInc[$module] && (self::$objLibLoadInc[$module] instanceof Lib_Glools_Loadinc) ){
            return self::$objLibLoadInc[$module];
        }
        
        self::$objLibLoadInc[$module] = Lib_Glools_Load::load('glools/loadinc', 'lib', $module);
        
        return self::$objLibLoadInc[$module];
    }
}
class Gtools extends Glools_Inc_Tools{};



function glools_runtime($ns='system', $name, $value=NULL){
	static $o = array();
    if (!isset($o[$ns])) {
    	$o[$ns] = array();
    }
    
    if (!isset($o[$ns][$name])) {
    	$o[$ns][$name] = $value;
    }
    
    if (empty($value)) {
    	return $o[$ns][$name];
    }
    $o[$ns][$name] = $value;
}

function glools_attachExtentionJS($code=null, $clear=false){
	//debug_record_data(__FUNCTION__ . '- before set', $code);
    $key = 'glools.extention.js';
    
    $cache = cache_get($key);
    
    if (empty($cache->data)) {
    	$cache->data = array();
    }
    //debug_record_data(__FUNCTION__ . '- after get cache', $cache->data);
    if ((null!=$code || !empty($code)) && !in_array($code, $cache->data)) {
    	$cache->data[] = $code;
    }
    
    $data = $cache->data;
    
    if (true===$clear) {
        $cache->data = array();
    }
    //debug_record_data(__FUNCTION__ . '- before set cache', $data);
    cache_set($key, $cache->data);
    //debug_record_data(__FUNCTION__ . '- return data', $data);
    return $data;
}

/**
 * parameters for $view:
 * $view = array(
 *     'view' => '[view path]'
 * );
 * */
function glools_load_view($view, $options){
	$objView = Lib_Glools_Load::load('glools/view', 'lib');
    $view = $objView->loadView($view, $options);
    
    if (!empty($view)) {
    	return drupal_render($view);
    }
    return null;
}


/**
 * This is a sample function for use view lib.
 * */
function glools_load_views($section, &$vars, $options=array(), $module=null) {
	
    if (empty($module) || !module_exists($module)) {
        throw new Exception("Provided module does not exist in system.");
    }
    
    $objView = Lib_Glools_Load::load('glools/view', 'lib', array('module'=>$module));
    
    $objView->loadPreset()->buildViews($section, $options);
    
    $objView->attachToPage($vars);
}

/**
 * Fix image style deliver bug for drupal core "image" module
 * Author: GloolsGuan@glools.com
 * Date:2013-1-2
 * Note: The super terrible drupal core code, It wasted my 24 hours for seeking and fixing the image style bug.
 * */
function glools_build_imageStyles($file){
    $styles = image_styles();
    if (!is_array($styles)) {
    	watchdog('error', 'Image styles have not been defined.');
        return false;
    }
    
    foreach ($styles as $style) {
        $paths = explode('://', $file->uri);
    	glools_image_style_deliver($style, $paths[0], $paths[1]);
    }
    return true;
}

/**
 * --Fix drupal core image.module:iamge_style_deliver bug--
 * Description:
 * After image upload, glools_file_insert or glools_file_update will invoke image_style_deliver for
 * create images depend on image style settings.
 * After creating images, the original image_style_deliver will transfer image data to client,
 * It is will issue a problem. So the code was disabled.
 * */
function glools_image_style_deliver($style, $scheme) {
  // Check that the style is defined and the scheme is valid.
  if (!$style || !file_stream_wrapper_valid_scheme($scheme)) {
    drupal_exit();
  }
  $args = func_get_args();
  array_shift($args);
  array_shift($args);
  $target = implode('/', $args);

  $image_uri = $scheme . '://' . $target;
  $derivative_uri = image_style_path($style['name'], $image_uri);
  
  // If using the private scheme, let other modules provide headers and
  // control access to the file.
  if ($scheme == 'private') {
    if (file_exists($derivative_uri)) {
      file_download($scheme, file_uri_target($derivative_uri));
    }
    else {
      $headers = module_invoke_all('file_download', $image_uri);
      if (in_array(-1, $headers) || empty($headers)) {
        return drupal_access_denied();
      }
      if (count($headers)) {
        foreach ($headers as $name => $value) {
          drupal_add_http_header($name, $value);
        }
      }
    }
  }

  // Don't start generating the image if the derivative already exists or if
  // generation is in progress in another thread.
  $lock_name = 'image_style_deliver:' . $style['name'] . ':' . drupal_hash_base64($image_uri);
  if (!file_exists($derivative_uri)) {
    $lock_acquired = lock_acquire($lock_name);
    if (!$lock_acquired) {
      // Tell client to retry again in 3 seconds. Currently no browsers are known
      // to support Retry-After.
      drupal_add_http_header('Status', '503 Service Unavailable');
      drupal_add_http_header('Retry-After', 3);
      print t('Image generation in progress. Try again shortly.');
      drupal_exit();
    }
  }

  // Try to generate the image, unless another thread just did it while we were
  // acquiring the lock.
  $success = file_exists($derivative_uri) || image_style_create_derivative($style, $image_uri, $derivative_uri);
  
  if (!empty($lock_acquired)) {
    lock_release($lock_name);
  }
  
  if ($success) {
    return true;
    //$image = image_load($derivative_uri);
    //file_transfer($image->source, array('Content-Type' => $image->info['mime_type'], 'Content-Length' => $image->info['file_size']));
  }
  else {
    watchdog('image', 'Unable to generate the derived image located at %path.', array('%path' => $derivative_uri));
    drupal_add_http_header('Status', '500 Internal Server Error');
    print t('Error generating image.');
    drupal_exit();
  }
}

function glools_form_ajax_render($form, $form_state){
    
    $selector = '.' . $form['#form_id'] . ' .form-item-' . $form_state['triggering_element']['#name'];
    
    $error = form_get_error($form_state['triggering_element']);
    $errors = form_get_errors();
    
    if (!empty($error)) {
        $commands = array(
            ajax_command_remove($selector . ' .validated'),
            ajax_command_append($selector, '<div class="validated">' . $error . '</div>'),
            ajax_command_css($selector . ' label', array('color'=>'#ff0000'))
        );
    }else{
        $commands = array(
            ajax_command_remove($selector . ' .validated'),
            ajax_command_css($selector . ' label', array('color'=>'#FF9900'))
        );
    }
    
    
    return $commands;
}

function theme_glools_form_wrapper($variables) {
    $element = $variables['element'];
    if (isset($element['#action'])) {
        $element['#attributes']['action'] = drupal_strip_dangerous_protocols($element['#action']);
    }
    element_set_attributes($element, array('method', 'id'));
    if (empty($element['#attributes']['accept-charset'])) {
        $element['#attributes']['accept-charset'] = "UTF-8";
    }
    return theme('glools_form_tpl', array('vars'=>$element));
    //return '<form' . drupal_attributes($element['#attributes']) . '><div>' . $element['#children'] . '</div></form>';
}


