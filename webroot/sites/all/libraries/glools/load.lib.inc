<?php
/*
 * Created on 29 Nov, 2011
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

require_once(dirname(__FILE__).'/base.lib.inc');

class Lib_Glools_Load extends Lib_Glools_Base{
    
    static public function load($path, $type, $options=null){
    	
        if( !is_string($path) || !is_string($type) ){
    		return false;
    	}
        
        $typePreset = self::loadTypePreset($type);
        
        $file = $typePreset['basePath'] . '/' . $path.$typePreset['fileSuffix'];
        
        if( !file_exists($file) ){
            return false;
        }
        
        return self::loadClass(self::buildClassName($typePreset['classPrefix'].'/'.$path), $file, $options);
    }
    
    static public function loadFile($path, $type){
        
    	$typePreset = self::loadTypePreset($type);
        
        $realFile = $typePreset['basePath'] . '/' . $path . $typePreset['fileSuffix'];
        
        if( !file_exists($realFile) ){
            return false;
        }
        
        require_once($realFile);
        return true;
    }
    
    
    static protected function loadTypePreset($type){
    	static $typeArgs ;
        $typeArgs = array(
            'lib' => array(
                'classPrefix' => 'lib',
                'basePath'    => DRUPAL_ROOT.'/sites/all/libraries',
                'fileSuffix'  => '.lib.inc'
            ),
            'view' => array(
                'classPrefix' => 'view',
                'basePath'    => DRUPAL_ROOT.'/sites/all/views',
                'fileSuffix'  => '.view.inc'
            ),
            'model' => array(
                'classPrefix' => 'model',
                'basePath'    => DRUPAL_ROOT.'/sites/all/models',
                'fileSuffix'  => '.model.inc'
            )
        );
        
        if( isset($typeArgs[$type]) ){
        	return $typeArgs[$type];
        }
        
        return null;
    }
    
    static protected function buildClassName($path){
    	$pathArgs = explode('/', $path);       
        $className = '';
        
        foreach( $pathArgs as $v ){
            $className .= ucfirst($v).'_';
        }
        
        return substr($className, 0, strlen($className)-1);
    }
    
    static protected function loadClass($name, $path, $options=null){
    	if( !is_string($name) || !is_string($path) ){
    		return false;
    	}
        
        include_once($path);
        
        if( !class_exists($name) ){
        	return false;
        }
        
        return new $name($options);
    }
}