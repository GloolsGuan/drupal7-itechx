<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Gtools\Base\Controller;


/**
 * Catalog product controller
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class Backend extends \Magento\Backend\App\AbstractAction
{
    
    protected $_module = null;
    
    protected $page_titles = [];
    
    protected $_yii_request = null;
    
    protected $_active_menu = null;
    
    
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        if ($request->isDispatched() && $request->getActionName() !== 'denied' && !$this->_isAllowed()) {
            $this->_response->setStatusHeader(403, '1.1', 'Forbidden');
            if (!$this->_auth->isLoggedIn()) {
                return $this->_redirect('*/auth/login');
            }
            $this->_view->loadLayout(['default', 'adminhtml_denied'], true, true, false);
            $this->_view->renderLayout();
            $this->_request->setDispatched(true);
            return $this->_response;
        }
        
        if ($this->_isUrlChecked()) {
            $this->_actionFlag->set('', self::FLAG_IS_URLS_CHECKED, true);
        }
        
        $this->_processLocaleSettings();
        //\Gtools\Debug::testLog(__METHOD__, [get_class($this->_request), $this->_request->getActionName()], __LINE__);
        return $this->defaultDispatch($request);
    }
    
    public function defaultDispatch(\Magento\Framework\App\RequestInterface $request){
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
    
    public function init(){}
    
    public function execute() {
        //\Gtools\Debug::testLog(__METHOD__, $this->_request->getActionName(), __LINE__);
        $action_method = springf('action%s', $this->_request->getActionName());
        $result = $this->$action_method();
        
        return $result;
    }
    
    
    public function setModule(\Gtools\Yii\AppModule $module){
        $this->_module = $module;
    }
    
    public function getModule(){
        return $this->_module;
    }
    
    
    public function setYiiRequest(\yii\web\Request $yii_request){
        $this->_yii_request = $yii_request;
    }
    public function getYiiRequest(){
        if(null==$this->_yii_request){
            throw new \Exception('System error: YII/Request is not exist.');
        }
        
        return $this->_yii_request;
    }
    
    public function getMageResultPage(){
        $result_page = \Gtools\Mage::createObject('\Magento\Framework\View\Result\PageFactory');
        
        return $result_page->create();
    }
    
    
    /**
     * Add page title
     * You can add more than one times.
     * @param type $title
     */
    public function addPageTitle($title){
        $this->page_titles[] = $title;
        return $this;
    }
    
    
    public function setActiveMenu($menu){
        $this->_active_menu = $menu;
    }


    /**
     *
     * @param type $view_name
     * @param type $data
     * @param string $layout Layout name, located in /themes/ebouti_backend/layouts
     * @return type
     */
    public function render($view_name, $data=[], $layout='default'){
        $resultPage = $this->getMageResultPage();
        //$resultPage->setActiveMenu('Magento_Catalog::catalog_products');
        if(!empty($this->page_titles)){
            foreach ($this->page_titles as $title){
                $resultPage->getConfig()->getTitle()->prepend($title);
            }
        }
        
        if(!empty($this->_active_menu)){
            $resultPage->setActiveMenu($this->_active_menu);
        }
        
        
        $default_block = $this->buildDefaultBlock($view_name, $data, $layout);
        
        /**
         * @see \Magento\Framework\View\Layout::addBlock();
         */
        $resultPage->getLayout()->addBlock($default_block, 'default_content', 'content');
        
        return $resultPage;
    }
    
    
    /**
     * template_path: catalog/product/edit.phtml
     * 
     * @param type $tpl
     * @param type $vars
     * @return type
     */
    protected function buildDefaultBlock($view_name, $data, $layout='default'){
        
        $block = new \Gtools\Yii\Widget();
        
        $base_view_path = sprintf('%s/views/%s/%s', $this->getModule()->getBasePath(), $this->getYiiRequest()->getMageParam('controller_name'), $this->getYiiRequest()->getmageParam('area_code'));
        //\Gtools\Debug::testLog(__METHOD__, $base_view_path, __LINE__);
        
        $block->setViewBasePath($base_view_path);
        $block->setViewName($view_name);
        $block->assign($data);
        
        $block_content = $block->buildContent();
        $layout_content = \Yii::$app->get('view')->render('//layouts/default.layout.php', ['content'=>$block_content]);
        
        //\Gtools\Debug::testLog(__METHOD__, $layout_content, __LINE__);
        $block->setCustomContent($layout_content);
        
        return $block;
    }
}
