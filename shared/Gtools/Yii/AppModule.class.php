<?php
namespace Gtools\Yii;
/* 
 * AppModule: Application module for user interaction
 */


class AppModule extends Module{
    
    
    protected $_controller = null;
    protected $_request_args = null;
    
    protected $_mage_action_factory = null;
    
    public $theme = 'ebouti_backend';
    
    public $base_view_path = '@app/../webroot/themes';
    
    
    
    const EVENT_INITIALIZE_REQUST = 'system/initialize_request';
    
    
    public function initEnv($area_code, $yii_request){
        $this->initApplicationEnv($area_code, $yii_request);
        $this->initUser($area_code);
        return true;
    }
    
    
    public function initApplicationEnv($area_code, $yii_request){
        $global_config = $this->loadGlobalApplicationConfig();
        $local_config = $this->loadConfig('main');
        $config = \yii\helpers\ArrayHelper::merge($global_config, $local_config);
        $config['id'] = $area_code;
        $config['basePath'] = \Yii::getAlias('@Mdu/..');
        \Yii::$app = new \Gtools\Yii\Application($config);
        
        //-- Set view path --
        $view_path = sprintf('%s/%s', $this->base_view_path, $this->theme);
        \Yii::$app->setViewPath($view_path);
        //\Gtools\Debug::testLog(__METHOD__, \Yii::$app, __LINE__);
    }
    
    
    public function initUser($area_code){
        $com_user = \Yii::$app->get('user');
        
        $com_user->initLoginUser($area_code);
        
        \Yii::$app->set('user', $com_user);
    }
    
    
    /**
     * Special for integrated with Magento2.x to Yii2
     */
    public function setControllerFactory($factory){
        $this->_mage_action_factory = $factory;
    }
    
    
    /**
     * 
     * @param type $controller_name
     * @param type $area_code
     * @param type $request_path The pure path without area_code, The path for permission checking.
     */
    public function loadController($controller_name, $area_code, $yii_request){
        $controller_ns = $this->buildControllerName($controller_name, $area_code);
        if(null==$this->_mage_action_factory){
            throw new \Exception('System error: Lost action factory of Magento2.x before loading controller in .');
        }
        //\Gtools\Debug::testLog(__METHOD__, $controller_ns, __LINE__);
        $controller = $this->_mage_action_factory->create($controller_ns);
        $controller->setModule($this);
        $controller->setYiiRequest($yii_request);
        
        
        $controller->init();
        $this->_controller = $controller;
        return $controller;
    }
    
    
    /**
     * 
     * @param type $request_path
     * @param type $area_code
     * @return boolean
     */
    public function hasAccessTo($request_path='', $area_code=''){
        //\Gtools\Debug::testLog(__METHOD__, $request_path, __LINE__);
        return true;
    }
    
    
    public function initRequest(\Gtools\Base\Controller\Base $controller, \Gtools\Yii\Request $request){
        $controller->setYiiRequest($request);
        
        $event = new \Gtools\Events\Module\InitializeRequest([
            'controller' => $controller,
            'request' => $request,
            'trigger_location' => __METHOD__
        ]);
        
        $this->trigger(self::EVENT_INITIALIZE_REQUST, $event);
        
        return $controller;
    }
    
    
    public function initController($controller, $request_args){
        $this->_controller = $controller;
        $this->_request_args = $request_args;
    }
    
    
    /**
     * Get Yii component
     * 
     * @return type
     */
    public function getCom($id, $args=[]){
        $com_ns = sprintf('Com\\%s', ucfirst($id));
        $com_reflection = new \ReflectionClass($com_ns);
        return $com_reflection->newInstance($args);
        
        //-- The original loading component system in yii is not working, because of without application instance created.
        //return $this->get($id);
    }
    
    
    protected function buildControllerName($controller_name, $area_code=''){
        
        $module_instance = new \ReflectionClass($this);
        //\Gtools\Debug::testLog(__METHOD__, [$module_instance, $module_instance->getShortName()], __LINE__);
        $name = $module_instance->getNamespaceName();
        $name = substr($name, strrpos($name, '\\')+1);
        $ns = sprintf('\Mdu\%s\Controller\%s\%s', $name, ucfirst($controller_name), ucfirst($area_code));
        
        return $ns;
    }
    
    
    protected function loadGlobalApplicationConfig(){
        $base_path = \Yii::getAlias('@Mdu/../includes/main.inc.php');
        //\Gtools\Debug::testLog(__METHOD__, $base_path, __LINE__);
        if(file_exists($base_path)){
            return include($base_path);
        }
        
        return [];
    }
    
    
    /**
     * 
     * 
     * @param type $file_name without extention name.
     * @param type $module
     * @return type
     * @throws \Exception
     */
    protected function loadConfig($file_name, $module=null){
        
        $module_name = empty($module) ? $this->getName() : $module;
        
        if(empty($file_name)){
            throw new \Exception('System error: ');
        }
        
        $base_path = \Yii::getAlias(sprintf('@Mdu/%s/etc/%s.inc.php', $module_name, $file_name));
        
        if(file_exists($base_path)){
            return include($base_path);
        }
        
        return [];
    }
    
}

