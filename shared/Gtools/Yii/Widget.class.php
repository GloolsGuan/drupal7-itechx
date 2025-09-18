<?php
namespace Gtools\Yii;
/* 
 * Widget, Web page plugins
 * 
 * The standalone content panel with data interacting supporting. You can regard it as web application.
 * 
 * All widget is running under module level.
 * The system running environment and RBAC is valid for widget state updating request.
 * 
 * ** [MOST IMPORTANT] The designed functionality target of "Widget" component **
 * The first is widget or block, It is an part of the page content.
 * The second, WIDGET is rich interactivity.
 * 
 * [ NOTE by @GloolsGuan at 2018/6/12 ]
 * Widget state working is base on WidgetStateSupporting component in APP/components, and now it is developing.
 * (: ~ :)
 * 
 * -- # About widget template variables # --
 * The widget is actrually component, you can set the property of widget for assign variables to template. 
 * The property used view template file must be public.
 * $widget is the instance of WIDGET class, used in view template for invoke widget public method.
 * 
 * -- # About widget state # --
 * You can think it as an simple action of controller for process some simple task.
 * Generally it is the supportor for master action page.
 * It is means your widget become an real application of web page.
 */

class Widget extends \yii\base\Widget{
    
    protected $view_name = 'default';
    
    protected $base_view_path = '';
    
    protected $state_request_api = '';

    protected $vars = [];
    
    protected $_view = null;
    /**
     * -- This property for widget state processing.
     * @var type 
     */
    protected $requst_state = [];
    
    protected $defaultExtension = 'tpl.php';
    
    protected $mage_properties = [];
    
    private $custom_content = '';
    
    
    /**
     * For magento2.x block supported.
     * 
     * custom_content for special render supporting.
     * 
     * @return string
     */
    public function toHtml(){
        if(!empty($this->custom_content)){
            //\Gtools\Debug::testLog(__METHOD__, $data, __LINE__);;
            return $this->custom_content;
        }
        
        return $this->buildContent();
    }

    
    public function processState($state='', $state_params=[]){
        
    }
    
    public function setCustomContent($content){
       if(!is_string($this->custom_content)){
           throw new \Exception('The content of parameter "$content" must be string.');
       } 
       
       $this->custom_content = $content;
       
       return $this;
    }
    
    public function buildContent(){
        //-- full view name is $view_name.html, The template should located in WIDGET/views/$view_name.html
        $view_full_path = $this->getViewFullPath($this->view_name);
        
        //$params = \Yii::getObjectVars($this);
        //$this->vars['widget'] = $this;
        //\Gtools\Debug::testLog(__METHOD__, [$this->view_name, $view_full_path, $this->vars], __LINE__);
        
        return $this->render($view_full_path, $this->vars);
    }
    
    
    public function setViewName($view_name){
        if(!is_string($view_name)){
            return false;
        }
        
        $view_name = trim($view_name);
        
        if(false==preg_match('#^[a-zA-Z\_][a-zA-Z\-\_\.]+$#', $view_name)){
            return false;
        }
        
        $this->view_name = $view_name;
    }
    
    public function assign($name, $value=''){
        
        if(!is_array($name) && !is_string($name)){
            $mes = sprintf('System error: Invalid name provide for %s, The value of $name should be string or array.', __MEHTOD__);
            throw new \Exception($mes);
        }
        
        if(is_array($name)){
            $assign_data = $name;
            $this->vars = \yii\helpers\ArrayHelper::merge($this->vars, $assign_data);
            return $this;
        }
        
        $this->vars[$name] = $value;
        return $this;
    }
    
    /**
     * @inherited
     * 
     * Return view view object. 
     */
    public function getView() {
        if(null==$this->_view){
            $this->_view = new \yii\web\View([
                'defaultExtension' => $this->defaultExtension
            ]);
            //$this->_view->defaultExtension = $this->defaultExtension;
        }
        
        return $this->_view;
    }
    
    
    /**
     * Generally, you don't need to running the method, The method existed for some special usage.
     * e.g: Render template in Controller\Action for supporting attaching content to Magento layout XML.
     * @see \Gtools\Base\Controller\Backend::render();
     * @param type $path
     */
    public function setViewBasePath($path=''){
        if(!is_string($path) || !is_dir($path)){
            Throw new \Exception('The base path for view must be string, and it is should under the rule of [MODULE_NAME]/views/[CONTROLLER_NAME]');
        }
        
        $this->base_view_path = $path;
    }
    
    /**
     * The view file should be located in the WIDGET/views directory.
     * 
     * @param type $view_name
     * @return type
     */
    protected function getViewFullPath($view_name){
        if(!empty($this->base_view_path)){
            $widget_location = $this->base_view_path;
        }else{
            $widget_location = $this->getWidgetLocation() . '/views';
        }
        
        $path = sprintf('%s/%s', $widget_location, $view_name);
        
        $new_path = str_replace('/mnt/www/demo.lasooo.com/application/modules', '@Mdu', $path);
        /*
        \Gtools\Debug::testLog(__METHOD__, [
            $path, \Yii::getAlias('Mdu'), $new_path
        ], __LINE__);
        */
        return $new_path;
    }
    
    protected function getWidgetLocation(){
        $widget_instance = new \ReflectionClass($this);
        return dir($widget_instance->getFileName());
    }
    
    /**
     * Magento2.x block system supporting method...
     */
    
    protected function setMageProperty($name='', $value){
        $this->mage_properties[$name] = $value;
    }
    
    public function setType($params){
        //\Gtools\Debug::testLog(__METHOD__, $params);
        $this->setMageProperty('element_type', $params);
    }
    
    public function setNameInLayout($name){
        $this->setMageProperty('name_in_layout', $name);
    }
    
    public function addData($data){
        //\Gtools\Debug::testLog(__METHOD__, $data, __LINE__);
        $this->setMageProperty('data', $data);
    }
    
    public function setLayout($layout){
        //\Gtools\Debug::testLog(__METHOD__, get_class($layout), __LINE__);
        $this->setMageProperty('layout', $layout);
    }
    
}