<?php
namespace service\weixin\official;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Yii;


class EventHandler extends \com\Component
{
    protected $handlers = array();
    
    
    
    public function __construct() {
        //$conf_handlers = Yii::$app->getParams('weixin_events_handlers');
    }
    
    
    
    public function process($event_type, $receiver)
    {
        $event_type = ucfirst(strtolower($event_type));
        
        $handler = sprintf('\frontend\components\weixin\handlers\%s', ucfirst($event_type));
        
        if (class_exists($handler)) {
            //GtoolsDebug::testLog(__FILE__, [$event_type, $receiver, $handler], __METHOD__);
            $e_handler = new $handler();
            $content = $e_handler->process($receiver);
            
            if (is_array($content) && array_key_exists('status', $content)) {
                return $content;
            }
            
            return $this->buildResponse('success', 200, $content);
        } else {
            Yii::warning('Weixin event handler does not exists.');
            GtoolsDebug::testLog(__FILE__, [$event_type, $receiver, $handler, 'Failed to checking by class_exists'], __METHOD__);
        }
    }
    
    
    
    public function registerHandler($event_type, $handler){
        if (!array_key_exists($event_type, $this->handlers)) {
            $this->handlers[$event_type] = array();
        }
        
        $this->handlers[$event_type][] = $handler;
    }
}