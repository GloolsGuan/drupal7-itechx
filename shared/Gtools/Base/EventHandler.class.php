<?php
namespace Gtools\Base;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class EventHandler implements \Magento\Framework\Event\ObserverInterface{
    
    
    /**
     * Execute EventHandler
     * 
     * Invoke process:
     * Magento\Framework\Event\Manager::dispatch();
     * Magento\Framework\Event\Invoker\InvokerDefault::dispatch() >> _callObserverMethod()
     * 
     * Note:
     * In magento2 system the name of "execute" is hard code, could not be change.
     * Now we will rename it as processEventHandler, and the callback depend on the predefine of events.xml
     * 
     * Event system concept:
     * event: Register or trigger and event, gennerally it is name of event.
     * behavor: the receiver of the event.
     * event handler: the processor of event, It is the same as Controller::dispatch();
     * 
     * <code>
     * <event name="controller_action_predispatch">
     *   <observer name="initialize" instance="Mdu\Weixin\Event\PreDispatchAction" />
     *   <observer name="checkAccess" instance="Mdu\Weixin\Event\PreDispatchAction" />
     * </event>
     * </code>
     * 
     * Event instance of $observer->getEvent(): @see \Magento\Framework\Event
     * 
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer){
        $default_handler = 'processEvent';
        $observer_name = str_replace(' ', '', $observer->getName());
        $behavior_name = sprintf('behavior%s', ucfirst($observer_name));
        
        if(method_exists($this, $behavior_name)){
            return $this->$behavior_name($observer->getEvent());
        }
        return $this->processEvent($observer->getEvent(), $behavior_name);
    }
    
    public function processEvent($event, $behavior_name){
        throw new \Exception(sprintf('The event handler "%s::%s" does not exists.', __CLASS__, $behavior_name));
    }
}