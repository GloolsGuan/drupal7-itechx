<?php
namespace Gtools\SmartModel;
/* 
 * Abstract bundle model for Modules extending.
 * 
 * ** Description **
 * Extend this class in your Model class for startup SmartModel supporting.
 * 
 * Before you extending the "AbstractBundleModel", What you need to do:
 * 1. Define your bundle structure file "schema".
 * 2. Make your Model class extends current class "AbstractBundleModel".
 * 
 * Service for:
 * 1. Auto update bundle structure for add new fields.
 * 2. Auto create bundle stable base on source management system rule @see Source.class.php or related documents.
 * 3. Load or retrieving resource.
 *  
 */

class Model extends \Gtools\Base\Model{
    
    
    protected $schema = null;
    
    protected $bundles = null;
    
    protected $resource_name = '';
    
    protected $errors = [];
    
    protected $fields = [];

    protected $attributes = [];
    
    public function __construct($resource_name, $schema=[], $config=[]){
        //$this->updateBundle($bundle_schema);
        if(!empty($resource_name)){
            $this->resource_name = $resource_name;
        }
        
        if(!empty($schema)){
            $this->schema = new Schema($schema);
        }
        //\Gtools\Debug::testLog(__METHOD__, $schema, __LINE__);
        
        parent::__construct($config);
    }
    
    
    public function setBundles($bundles){
        
        if(!is_array($bundles) || empty($bundles)){
            throw new \Exception('Failed to set bundles.');
        }
        //\Gtools\Debug::testLog(__METHOD__, $bundles, __LINE__);
        
        foreach($bundles as $name=>$bundle){
            $this->bundles[$name] = $bundle;
        }
    }
    
    
    /**
     * Build field map 
     * 
     */
    public function buildField($field_name, $field_settings){
        $field_com_name = $field_settings['type'];
        $field_settings['field_name'] = $field_name;
        
        return $this->loadFieldCom($field_com_name, $field_settings);
    }
    
    
    public function updateBundle(){
        
    }
    
    
    public function buildBundle(){
        
    }
    
    
    public function loadBundle(Models\Resource $resource=null){
        return new Models\Bundle();
    }
    
    
    
    
    public function buildBundleTable(){
        
    }
    
    
    public function createResource(){
        
    }
    
    
    /**
     * Get attribute of model for db operating
     */
    public function getAttribute($name){
        if($this->hasField($name)){
            return $this->getField($name)->getValue();
        }
    }
    
    public function buildSelect($conditions){
        
    }
    
    
    /**
     * In SmartModel system, table name including resource table name and bundle table name(If exists.)
     */
    public function getTableNames(){
        
    }
    
    
    public function saveResource(){
        // Saving resource.
        //$resource_id = $this->save();
        //$this->getBundle()->setAttributes($this->getBundleAttributes());
        //$this->getBundle()->resource_id=$resource_id;
        //
        // Saving bundle.
    }
    
    
    /**
     * Get field values by xpath, 'resource|resource/field' | 'bundle|bundle/field'
     * @param type $xpath
     */
    public function loadFieldValues($xpath){
        
    }
    
    
    /**
     * Is resource exsits?
     * 
     * @param type $name
     * @param type $owner
     */
    public function hasResource($name, $owner){
        
    }
    
    
    /**
     * 当获取Form时才需要对字段进行渲染构造
     * 
     * @return \Gtools\SmartModel\Models\Form
     */
    public function getForm($form_state=[]){
        //-- Merge resource and bundle fields --
        //$fields = \yii\helpers\ArrayHelper::merge($this->schema[''], $b);
        
        $fields_schema = [];
        $fields = $this->buildFields($form_state);
        
        //TODO: Seeking field with 'editable' properties 
        
        return new Models\Form($this->resource_name, $fields, []); 
    }
    
    
    
    
    
    /**
     * Get all registered field objects
     */
    public function getFields(){
        return $this->fields;
    }
    
    
    public function getField($field_name){
        return $this->fields[$field_name];
    }
    
    
    public function buildFields($form_state=[]){
        //\Gtools\Debug::testLog(__METHOD__, $this->schema, __LINE__);
        if(empty($this->schema)){
            return false;
        }
        
        //\Gtools\Debug::testLog(__METHOD__, $this->schema, __LINE__);
        
        $fields = $this->schema->getFields();
        //\Gtools\Debug::testLog(__METHOD__, $fields, __LINE__);
        $bundles = $this->schema->getBundles();
        $bundles['base'] = $fields;
        //\Gtools\Debug::testLog(__METHOD__, $bundles, __LINE__);
        foreach($bundles as $bundle_name=>$fields){
            $group = [];
            array_walk($fields, function($field_settings, $field_name) use(&$group){
                //\Gtools\Debug::testLog(__METHOD__, [$field_name, $field_settings], __LINE__);
                $field_settings['field_name'] = $field_name;
                $field_type = $field_settings['field_type'];
                unset($field_settings['field_type']);
                $group[$field_name] = $this->loadFieldCom($field_settings['field_type'], $field_settings);
            });
            $this->fields[$bundle_name] = $group;
        }
        
        \Gtools\Debug::testLog(__METHOD__, $this->fields, __LINE__);
    }
    
            
    public function save($runValidation = true, $attributeNames = NULL){
        
    }
    
    
    public function loadResource($resource_id, $resource_name){
        //\Gtools\Debug::testLog(__METHOD__, [$resource_id, $resource_name], __LINE__);
        
        try{
            $resource_entity = $this->_loadResource($resource_id, $resource_name);
            $resource_model = $this->buildResource($resource_entity, $resource_name);
        }catch(\Exception $e){
            return $this->buildResponse(500, 'failed', $e->getMessage());
        }
        
        return $this->buildResponse(201, 'success', $resource_model);
    }
    
    
    protected function buildResource($resource_entity, $resource_name=null){
        return new Models\Resource();
    }
    
    
    /**
     * Load resource and bundle data from database
     * 
     */
    protected function _loadResource($resource_id, $resource_name){
        return [
            'hi' => 'Hello,world!'
        ];
    }
    
    
    public function retrieve($resource_name, $query){
        
    }
    
    
    public function remove(){
        
    }
    
    /**
     * Hard remove record
     */
    public function delete(){
        
    }
    
    public static function tableName() {
        return 'resource_sample';
    }
    
    
    public function buildResponse($code, $status, $data){
        return [
            'code'=>$code,
            'status' => $status,
            'data' => $data
        ];
    }
    
    
    protected function loadFieldCom($com_name, $field_settings){
        $field_com_ns = sprintf('%s\Coms\Fields\%s\%s', __NAMESPACE__, ucfirst($com_name), ucfirst($com_name));
        //\Gtools\Debug::testLog(__METHOD__, $field_com_ns, __LINE__);
        
        return \Yii::createObject($field_com_ns, $field_settings);
        
    }
    
}