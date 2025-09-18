<?php
namespace Gtools\Base\Controller;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Base extends \Magento\Framework\App\Action\Action{
    
    
    public $area_code = '';
    
    public $query_tags = [];
    
    public $request_path = '';
    
    protected $post_state = [];

    protected $_module = null;
    
    protected $request = null;
    
    protected $_yii_request = null;



    public function execute(){}
    
    public function init(){}
    
    public function dispatch(\Magento\Framework\App\RequestInterface $request) {
        $this->_request = $request;
        $profilerKey = 'CONTROLLER_ACTION:' . $request->getFullActionName();
        $eventParameters = ['controller_action' => $this, 'request' => $request];
        $this->_eventManager->dispatch('controller_action_predispatch', $eventParameters);
        $this->_eventManager->dispatch('controller_action_predispatch_' . $request->getRouteName(), $eventParameters);
        $this->_eventManager->dispatch(
            'controller_action_predispatch_' . $request->getFullActionName(),
            $eventParameters
        );
        \Magento\Framework\Profiler::start($profilerKey);

        $result = null;
        
        $action_method = sprintf('action%s', ucfirst($this->getYiiRequest()->getMageParam('action_name')));
        //\Gtools\Debug::testLog(__METHOD__, [$action_method, $this->getYiiRequest()->getMageParam('action_name') ,$request->getFullActionName(),get_class($this)], __LINE__);
        if (false==method_exists($this, $action_method)) {
            throw new \Exception(sprintf("Request error: your request '%s' is invalid, The action is not exists.!", $request->getPathInfo()));
        }
        
        if ($request->isDispatched() && !$this->_actionFlag->get('', self::FLAG_NO_DISPATCH)) {
            \Magento\Framework\Profiler::start('action_body');
            $this->init();
            $result = $this->$action_method();
            \Magento\Framework\Profiler::start('postdispatch');
            if (!$this->_actionFlag->get('', self::FLAG_NO_POST_DISPATCH)) {
                $this->_eventManager->dispatch(
                    'controller_action_postdispatch_' . $request->getFullActionName(),
                    $eventParameters
                );
                $this->_eventManager->dispatch(
                    'controller_action_postdispatch_' . $request->getRouteName(),
                    $eventParameters
                );
                $this->_eventManager->dispatch('controller_action_postdispatch', $eventParameters);
            }
            \Magento\Framework\Profiler::stop('postdispatch');
            \Magento\Framework\Profiler::stop('action_body');
        }
        \Magento\Framework\Profiler::stop($profilerKey);
        return $result ?: $this->_response;
    }
    
    
    public function getModule($name=''){
        if(empty($this->_module) || !($this->_module instanceof \Gtools\Yii\AppModule)){
            throw new \Exception('System error, The base application module is not exists.');
        }
        
        if(empty($name)){
            return $this->_module;
        }
        
        return $this->this->_module->loadModule($name);
    }
    
    
    public function setModule(\Gtools\Yii\AppModule $module){
        $this->_module = $module;
    }
    
    public function setRequestParams($params){
        $this->request_params = $params;
    }
    
    /**
     * Set YiiReqest by \Gtools\Router::match();
     * 
     * @param \yii\web\Request $yii_request
     */
    public function setYiiRequest(\yii\web\Request $yii_request){
        $this->_yii_request = $yii_request;
    }
    public function getYiiRequest(){
        if(null==$this->_yii_request){
            throw new \Exception('System error: YII/Request is not exist.');
        }
        
        return $this->_yii_request;
    }
    
    
    protected function getPostState(){
        static $__post_state;
        
        if(empty($__post_state)){
            $input_post_state = $this->getYiiRequest()->getJsonPost();
            if(!empty($_POST)){
                $post_state = $_POST;
            }else if(!empty($input_post_state)){
                $post_state = $input_post_state;
            }else{
                $post_state = null;
            }
            
            $__post_state = empty($post_state) ? null : (new \Magento\Framework\DataObject($post_state));
        }
        
        //\Gtools\Debug::testLog(__METHOD__, [$_POST, $input_post_state], __LINE__);
        return $__post_state;
    }
    
    
    public function buildResponse($content){
        if (!is_string($content)){
            throw new \Exception("System error: Invalid response content rendered.");
        }
        
        return $this->getResponse()->appendBody($content);
    }
    
    
    public function buildJsonResponse($content){
        if (!is_string($content) && !is_array($content)) {
            //\Gtools\Debug::testLog(__METHOD__, $content, __LINE__);
            throw new \Exception('System error: The content of "Json Response" must be string or array.');
        }
        
        $json_string = json_encode($content);
        
        return $this->renderString($json_string);
    }
    
    
    public function renderString($content=''){
        if (!is_string($content) && !is_array($content)) {
            //\Gtools\Debug::testLog(__METHOD__, $content, __LINE__);
            throw new \Exception('System error: The content of "Json Response" must be string or array.');
        }
        return $this->getResponse()->appendBody($content);
    }
    
    
    public function getCom($com_name){
        
    }
    
    
    
    public function render($view_name, $data=[]){
        
    }
    
    
    
}