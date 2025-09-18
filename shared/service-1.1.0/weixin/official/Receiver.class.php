<?php
namespace service\weixin\official;

/*
 * Receive event message from weixin and parse message
 * 
 * ---
 */


use yii\base\Component;
use stdClass;

class Receiver extends \yii\base\Component
{
    
    protected $request = null;
    protected $raw_xml = null;
    protected $entity   = null;
    
    public $mes_type = null;
    
    public function __construct($request, $raw_xml){
        $this->request = $request;
        $this->raw_xml = $raw_xml;
        //\GtoolsDebug::testLog(__FILE__, [$request->get(), $request->get('echostr'), empty($request->get('echostr'))], __METHOD__);
        if (!empty($raw_xml)) {
            $this->entity = $this->_parse($raw_xml);
            $this->mes_type = $this->entity['MsgType'];
        } else if (!empty($request->get('echostr'))){
            $this->mes_type = 'test';
            $this->entity = $request->get();
        } else {
            $this->mes_type = 'invalid_event_notice';
        }
        
        /*
        $entry_hook = sprintf('msgType%sProcess', ucfirst($entry['MsgType']));
        $this->mes_type = $emtry['MsgType'];
        
        if (method_exists($this, $entry_hook)) {
            $this->$entry_hook($entry);
        }
        
        $this->entry = $entry;
        */
    }
    
    
    
    public function __get($name){
        if (array_key_exists($name, $this->entry)) {
            return $this->entry[$name];
        }
    }
    
    
    
    public function getAll(){
        return $this->entry;
    }
    
    
    
    
    protected function _parse($raw_xml){
        $entry = array();
        
        $requestArr = (array)simplexml_load_string($raw_xml, 'SimpleXMLElement');
        foreach ($requestArr as $name=>$element) {
            $entry[$name] = (string)$element;
        }

        return $entry;
    }
    
    
    
    //-- ---------------------------------------------------------------------//
    //-- The following method are hooks for processing special Weixin message //
    //-- ---------------------------------------------------------------------//
    
    /**
     * Image message type process
     * @param type $entry
     */
    protected function msgTypeImageProcess(&$entry){
        
        $dist_src = sprintf('%s/%s/user_%s', DIR_APP_WEBROOT, 'assets-ext/wx', $entry['FromUserName']);
        $file_name = $entry['MsgId'] . '.jpg';
        
        //-- Try to build directory --
        if (!is_dir($dist_src)) {
            mkdir($dist_src, 0775, true);
            chmod($dist_src, 0775);
        }
        
        if (!is_writable($dist_src)) {
            $entry['error'] = sprintf('Error, The image folder "%s" is not writable.', $dist_src);
            GtoolsDebug::sysLog(__FILE__, $entry['error'], __METHOD__);
        }
        
        //-- Load image from Weixin server --
        $image_content = file_get_contents($entry['PicUrl']);
        if (empty($image_content)) {
            $entry['error'] = sprintf('Failed to load image from Weixin server with url %s . ', $entry['PicUrl']);
            GtoolsDebug::sysLog(__FILE__, $entry['error'], __METHOD__);
        }
        
        //-- Save image to local --
        $file_path = sprintf('%s/%s', $dist_src, $file_name);
        $fp = fopen($file_path, 'wb');
        if (false==$fp) {
            $entry['error'] = sprintf('Failed to copy weixin image from weixin server to local with local direcotry "%s"', $dist_src);
            GtoolsDebug::sysLog(__FILE__, $entry['error'], __METHOD__);
            return;
        }
        $is_writed = fwrite($fp, $image_content);
        fclose($fp);
        
        $entry['LocalUri'] = str_replace(DIR_APP_WEBROOT, '', $file_path);
    }
}