<?php
/**
 * The core component between magento and yii2 framework.
 */
namespace Gtools;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * Config primary
     *
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Url
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;
    
    protected $_areaList;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_pageFactory = $pageFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
        
        $oManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_areaList = $oManager->create(\Magento\Framework\App\AreaList::class);
    }

    
    public function match(\Magento\Framework\App\RequestInterface $request) {
        
        $request_path = $request->getPathInfo();
        $front_name = $request->getFrontName();
        $area_code = $this->_areaList->getCodeByFrontName($front_name);
        if('adminhtml'==$area_code){
            $area_code = 'backend';
            //array_shift($path_args);
        }
        //\Gtools\Debug::testLog(__METHOD__, [$front_name, $area_code, $request->getDistroBaseUrl(), $request->getDistroBaseUrlPath($_SERVER)], __LINE__);
        $path_args = explode('/', ltrim($request_path, '/'));
        
        //\Gtools\Debug::testLog(__METHOD__, [$path_args, $path_args[0], $front_name], __LINE__);
        if($path_args[0]==$front_name) {
            array_shift($path_args);
        }
        
        /*
        \Gtools\Debug::testLog(__METHOD__, [
            'front_name' => $front_name,
            'area_code' => $area_code,
            'request_path' => $request_path,
            'path_args' => $path_args
        ], __LINE__);
        */
        
        $module_name = !empty($path_args[0]) ? $path_args[0] : 'default';
        $controller_name = !empty($path_args[1]) ? $path_args[1] : 'front';
        $action_name = !empty($path_args[2]) ? $path_args[2] : 'index';
        
        $request->setModuleName($module_name)->setControllerName($controller_name)->setActionName($action_name);

        $query_tags = array_slice($path_args, 3);
        $module_ns = sprintf('\\Mdu\\%s\\Module', ucfirst($module_name));
        $controller_ns = sprintf('\\Mdu\\%s\\Controller\\%s\\%s', ucfirst($module_name), ucfirst($controller_name), ucfirst($area_code));
        
        \Yii::autoload($controller_ns);
        if (!class_exists($controller_ns)){
            throw new \Exception(sprintf('System error: your request is invalid the controller "%s" is not exists.', $controller_ns));
        }
        
        $yii_request = $this->getYiiRequest([
            'base_url' => $request->getDistroBaseUrl(),
            'front_name' => $front_name,
            'area_code' => $area_code,
            'module_name' => $module_name,
            'controller_name' => $controller_name,
            'action_name' => $action_name,
            'request_path' => sprintf('%s/%s/%s', $module_name, $controller_name, $action_name),
            'query_tags' => $query_tags
        ]);
        
        
        /**
         * -- Initialize request --
         * 
         */
        $module = new $module_ns();
        $module->initEnv($area_code, $yii_request);
        $module->setControllerFactory($this->actionFactory);
        
        //-----------
        if(false==$module->hasAccessTo(implode('/', array_slice($path_args, 0, 3)), $area_code)){
            //-- Error controller returned --
            //TODO
            $controller = $module->loadController($controller_name, $area_code, $yii_request);
        } else {
            $controller = $module->loadController($controller_name, $area_code, $yii_request);
        }
        
        //\Gtools\Debug::testLog(__METHOD__, get_class($controller), __LINE__);
        
        return $controller;
    }
    
    
    public function getYiiRequest($params){
        return new \Gtools\Yii\Request(['mage_params'=>$params]);
    }
    
    
}
