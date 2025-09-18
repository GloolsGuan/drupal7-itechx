<?php
namespace service\gevent;
/* 
 * Global business event service 全平台业务事件服务
 * 
 * 该事件服务对接各应用内部的事件部署，并构建统一的事件注册、行为管理等机制。
 * 该事件服务的运行依赖异步任务服务，也就是说部分非及时事件触发的行为会被以异步
 * 任务的模式运行，同时为了保持事件体系的有效运行设置一个激活的事件最多只能触发
 * 的行为个数，可以有效的解决这个问题，默认的行为数量阈值是10。
 * 
 * 关于实时行为与异步行为机制：
 * 在YII系统中的“行为（behavior）”在全环境业务事件体系中，也称作任务，取决于同步还是异步运行。
 * 如果是同步运行的“行为”可以接收到基于当前SESSION的环境参数，并做出有效的实施响应。
 * 异步“行为”这里称为“任务”的运行脱离当前SESSION环境，唯一的参数就是事件触发时的event对象。
 * 异步运行的“任务”是在无YII-WEB框架启动环境下运行，也就是纯命令行，YII2框架作为默认组件库支持，
 * 也就是说你依然可以使用\yii\xxx 之类的对象和方法完成任务，但是\Yii::$app 是无效的。
 * **但是整个"service"体系都可正常使用。**
 * 
 * Gevent事件体系默认基于REDIS运行，需要REDIS开启独立的SERVICE支持。
 */

class Gevent extends \service\base\Module{
    
    public $max_event_handlers = 10;
    public $redis_port = 3399;
    
    
    public function loadEvent(){
        
    }
    public function loadEventHandler($app_id, $event_name){
        
    }
    
    public function registerEventHandler($app_id, $event_name, $behavior){
        
    }
    
    
    public function registerEvent($app_id, $event_name, $options=[]){
        
    }
    
    public function registerHandlerAsTask($app_id, $event_name, $behavior){
        
    }
}