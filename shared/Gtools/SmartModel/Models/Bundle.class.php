<?php
namespace Gtools\SmartModel\Models;
/* 
 * Bundle：数据结构描述与管理者
 * 
 */

class Bundle extends \yii\base\Model{
    
    /**
     * Bundle technical name, used for build resource table
     * name rule "[a-z][a-z0-9\_]{0,30}"
     * @var type 
     */
    public $name = '';
    
    /**
     * Readable title for bundle managing
     * @var type 
     */
    public $title='';
    
    /**
     * The bundle and bundle resource owner, Only Business module namespace is allowed, replace "\\" to "/"
     * @var type 
     */
    public $owner = '';
    
    /**
     * Short brief for introducing the bundle
     * @var type 
     */
    public $brief= '';
    
    /**
     * Bundle version
     * The same name of bundle allowed lots of versions, but only one is activity.
     * @var type 
     */
    public $version = '';
    
    /**
     * Bundle fields defination
     * @var type 
     */
    public $fields = [];
    
    /**
     * Activity values for fields
     * @var type 
     */
    public $values = [];
    
    
    /**
     *Group structure 
     * 
     * @var type 
     */
    public static $groups = [];
    
    
    public function attachFields($field_definations){
        
    }

    
    public function attachField($label, $name, $defination=[]){
        
    }
    
    
    public function getFieldDefination($name){
        
    }
    
    
    /**
     * 
     * @return \Gtools\SmartModel\Models\Form
     */
    public function getForm(){
        
        return new Form();
    }
    
    
    /**
     * Output bundle element structure as html code
     */
    public function render(){
        
    }
    
    
    /**
     * Render bundle field structure as html code
     * @param type $name
     * @param type $attributes
     */
    public function renderField($name, $attributes=[]){
        
    }
    
    
    /**
     * Render grouped fields as html code
     * 
     * @param type $group_name
     * @param type $attributes
     */
    public function renderGroup($group_name, $attributes=[]){
        
    }
    
    
    /**
     * 
     */
    protected function buildForm($group_first=false){
        if(empty($fields)){
            return [];
        }
        
        
        $nodes = [];
        foreach($this->fields as $name=>$field){
            $nodes[$name] = $field->buildElement(
                $this->loadFieldValue($name)
            );
            
            if($field->hasGroup()){
                $this->buildGroup($name, $field->getGroup());
            }else{
                $this->buildGroup($name, 'ungrouped');
            }
        }
        
        $model_form = new Form([
            'nodes'=>$nodes, 
            'groups'=>$this->loadGroups(), 
            'group_first'=>$group_first
        ]);
        
        return $model_from;
    }
    
    protected function buildGroup($field_name, $group_name){
        if(!\yii\helpers\ArrayHelper::keyExists($group_name, self::$groups)){
            self::$groups[$group_name] = [];
        }
        
        self::$groups[$group_name][] = $field_name;
        
        return this;
    }


    /**
     * Transfer bundle field defination to form node
     */
    protected function buildFromNode($name, $settings){
        return [
            $name => []
        ];
    }
}