<?php
namespace Ebouti\Business;
/* 
 * 综合资源服务
 * 
 * 资源服务项目包含商品、活动、服务等
 */



class Resource extends \Gtools\Yii\Module{
    
    
    public function init(){
        $this->initComs();
    }
    
    
    /**
     * Initialize supported components
     */
    public function initComs(){
        $resource_conf = include(__DIR__ . '/includes/BizComs.inc.php');
        $this->setComponents($resource_conf);
    }
    
    
    public function load($biz_id, $biz_type=''){
        
        $com_biz = $this->get($biz_type);
        \Gtools\Debug::testLog(__METHOD__, [$biz_type, $com_biz], __LINE__);
        $com_biz->load($biz_id);
    }
    
    
    public function loadResource($biz_id, $biz_type){
        return $this->load($biz_id, $biz_type);
    }
    
    
    /**
     * Load bundle model for building 
     * 
     * Model对资源进行管理，资源需要有数据结构声明
     * 
     * Model是数据服务组件，主要数据提取、结构构件、数据存储等相关操作。
     * 在SmartModel中，Model的结构依赖于当前资源的Schema定义。
     */
    public function loadModel($biz_name, $biz_id=''){
        if(!empty($biz_id) && is_string($biz_id)){
            $resource_model = $this->loadResource($biz_id, $biz_name);
            //-- Return model for build form --
            return $this->buildResponse(201, 'success', $resource_model);
        }
        
        if(!$this->has($biz_name)){
            return $this->buildResponse(201, 'failed', 'Business type is not supported.');
        }
        
        $com_biz = $this->get($biz_name);
        if(!$this->hasResource($com_biz->resource_name)){
            throw new \Exception('Business resource is not exists.');
        }
        
        $schema = $this->loadBizSchema($com_biz->resource_name, $biz_name);
        
        
        $resource_model = new Models\Resource($com_biz->resource_name, $schema);
        //\Gtools\Debug::testLog(__METHOD__, $resource_model, __LINE__);
        return $this->buildResponse(201, 'success', $resource_model);
    }
    
    
    protected function hasResource($resource_name){
        return $this->getSmartModel()->hasResource($resource_name);
    }
    
    
    
    public function loadBizSchema($resource_name, $biz_name=null){
        
        $cache = \Gtools\Cache::getServer();
        $cache_id = $this->buildCacheId('resource', [$resource_name, $biz_name]);

        //-- Disabled for dev
        //if($cache->exists($cache_id)){
        if(false){
            $cached_schema = $cache->hGetAll($cache_id);
            $schema = [];
            foreach($cached_schema as $k=>$v){
                //\Gtools\Debug::testLog(__METHOD__, [$cache_id, $k, $v], __LINE__);
                if(empty($biz_name) || 'resource'==$k){
                    $schema[$k] = unserialize($v);
                    continue;
                }
                
                if(false!==strrpos($k, $biz_name)){
                    $schema[$k] = unserialize($v);
                }
            }
            //\Gtools\Debug::testLog(__METHOD__, $schema, __LINE__);
            return $schema;
        }
        
        $schema = $this->loadSchema($resource_name);
        
        $cache->hSet($cache_id, 'resource', serialize($schema));
        
        if(!empty($biz_name)){
            $schema['bundles'] = [];
            $schema['bundles'][$biz_name] = $this->get($biz_name)->loadSchema();
            $cache->hSet($cache_id, $biz_name, serialize($schema['bundles'][$biz_name]));
        }
        
        //\Gtools\Debug::testLog(__METHOD__, $schema, __LINE__);
        return $schema;
    }
    
    
    public function buildCacheId($type, $keys){
        
        $key_str = is_string($keys)? $keys : implode('/', $keys);
        
        $cache_id = sprintf('%s:%s:%s', $this->getName(), $type, $key_str);
        //\Gtools\Debug::testLog(__METHOD__, [$cache_id, $this->getName()], __LINE__);
        return $cache_id;
    }
    
    
    /**
     * 构建Schema模型
     * 
     * Bundle属于主资源类型的附属数据表，不是必须，单可以通过主表与附属表数据结构规范业务类型，
     * 并提供该业务类型下多种内容结构的变种。
     * 
     * @param type $resource_schema
     * @param type $bundle_schema
     */
    protected function buildSchemaModel($resource_schema, $bundle_schema=[]){
        
        try{
            $schema = new \Gtools\SmartModel\Schema($resource_schema, $bundle_schema);
        }catch(\Exception $e){
            return $e->getMessage();
        }
        
        return $schema;
    }




    protected function _loadBizSchema($biz_type){
        $com = $this->loadResourceComponent($biz_type);
        
        $resource_schema = $this->loadSchema();
        
        $resource_schema['bundle'] = [];
        $resource_schema['bundle'][$biz_type] = $com->loadSchema();
        
        return $resource_schema;
    }




    /**
     * Build empty bundle 
     * 
     * @param type $biz_type
     * @param \Gtools\SmartModel\Model\Resource $resource
     * @return type
     */
    protected function buildBundle($biz_type){
        
        $com_resource = $this->loadResourceComponent($biz_type);
        
        //-- Build component level bundle --
        $bundle_schema = $com_resource->loadSchema();
        
        //-- Is valid resource type? --
        $bundle_resource_type = \yii\helpers\ArrayHelper::getValue($bundle_schema, 'resource_type');
        if(empty($bundle_schema)){
            throw new \Exception('Invalid resource type.');
        }
        
        $mdu_smart_model = $this->getSmartModel();
        
        return $mdu_smart_model->createBundle($bundle_resource_type, $biz_type, $bundle_schema);
    }


    /**
     * Retrieving a list of items.
     * 
     * @param type $query
     */
    public function retrieve($biz_type, $query=[]){
        $resource_com_ns = $this->buildResourceNamespace($biz_type);
        $com_resource = $this->loadComponent($resource_com_ns);
        $model_resource = $com_resource->loadModel();
        $re = $model_resource->retrieve($query, $mes);
        if(false==$re){
            return $this->buildResponse(400, 'error', $mes);
        }elseif(empty($re)){
            return $this->buildResponse(200, 'success', $re);
        }
        
        return $this->buildResponse(201, 'success', $re);
    }
    
    
    public function buildForm($biz_type, $form_state=[]){
        
    }
    
    
    /**
     * 
     */
    public function retrieveRemovedItems($user_id='', $biz_type=''){
        
    }
    
    
    /**
     * Load resource varieties
     * 
     * 
     */
    public function loadVarieties(){
        
    }
    
    /**
     * Creating an new item of an business
     */
    public function createItem($model_entity, $biz_type=''){
        
    }
    
    
    public function updateItem($biz_id, $model_entity, $biz_type=''){
        
    }
    
    
    public function processAction($biz_id, $action, $params, $user_id){
        
    }
    
    
    public function hasAccess(){
        
    }
    
    
    protected function buildResourceNamespace($biz_type){
        $biz_com_name = ucfirst($biz_type);
        return sprintf('%s\Coms\Resources\%s\%s', __NAMESPACE__, $biz_com_name, $biz_com_name);
    }
    
    
    protected function loadBizCom($biz_name=''){
        $com_ns = $this->buildResourceNamespace($biz_name);
        $this->loadComponent($com_ns, ['resources'=>$this->loadResources(), 'module'=>$this]);
    }
    
    
    protected function getSmartModel(){
        
        return \Yii::createObject('\Gtools\SmartModel\Module', []);
    }
    
    
    protected function loadSchema(){
        $schema_file = __DIR__ . '/includes/BusinessResource.schema';
        if(!file_exists($schema_file)){
            throw new \Exception(sprintf('Lost resource schema file "%s"', $schema_file));
        }
        
        return include(__DIR__ . '/includes/BusinessResource.schema');
    }
}