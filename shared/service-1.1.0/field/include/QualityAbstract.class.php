<?php

/*
 * 
 * 
 * 
 */

namespace app\widgets\field\field\inc;

class QualityAbstract{
    
    public $schema = null;
    public $label = '';
    public $name = '';
    public $depend_on = array();
    public $fields = array();
    public $category = '';
    
    protected $model_quality = null;
    
    public function __construct($config, $params) {
        Yii::import('modules.quality.model.QualityModel');
        
        parent::__construct($this->name, null, $config);
        
        $this->setParams($params);
        
        $this->model_quality = new QualityModel();
    }
    
    
    public function getUserQuality($user_id=''){
        if (empty($user_id) || !is_numeric($user_id)) {
            return false;
        }
        //Lib_Gtools_Debug::testLog(__FILE__, array($this->name, $user_id), __METHOD__);
        return $this->model_quality->loadUserQuality($this->name, $user_id);
    }
    
    public function buildWidget($field_values){
        $fields = $this->fields;
        $build = array();
        if (!is_array($fields)) {
            throw new Exception('Failed, Field settings is invalid, ' . __METHOD__);
        }
        
        Yii::import('modules.field.FieldModule');
        $md_field = new FieldModule();
        
        //Lib_Gtools_Debug::testLog(__FILE__, $field_values, __METHOD__);
        
        foreach($fields as $field_name=>$field) {
            $value = (is_array($field_values) && array_key_exists($field_name, $field_values)) ? $field_values[$field_name] : null;
            $build[$field_name] = array(
                'field'=>$field,
                'build'=>$md_field->buildField($field_name, $field, $value)
            );
            
        }
        
        return $this->buildForm($build);
    }
    
    
    public function loadFieldElements(){
        $fields = $this->fields;
        $build = array();
        
        if (!is_array($fields)) {
            throw new Exception('Failed, Field settings is invalid, ' . __METHOD__);
        }
        
        Yii::import('modules.field.FieldModule');
        $md_field = new FieldModule();
        
        foreach($fields as $field_name=>$field) {
            $build[$field_name] = array(
                'field'=>$field,
                'build'=>$md_field->buildField($field_name, $field)
            );
        }
        
        return $build;
    }
    
    
    
    public function buildForm($build_fields) {
        
        $form = array(
            'form_name' => 'modules_quality_' . $this->name,
            'token' => Form::getToken('modules_quality_' . $this->name)
        );
        
        $params = $this->params->toArray();
        ob_start();
        include(dirname(__FILE__) . '/../templates/form.tpl.php');
        $content = ob_get_clean();
        return $content;
    }
    
    public function save($user_id, $data){
        Yii::import('modules.quality.model.QualityModel');
        Yii::import('modules.field.FieldModule');
        $md_field = new FieldModule();
        $model = new QualityModel();
        
        if (empty($user_id) || !is_numeric($user_id)) {
            return false;
        }
        
        $field_keys = array_keys($this->fields);
        $quality_entry = array();
        
        foreach($data as $k=>$v) {
            if (!in_array($k, $field_keys)) {
                continue;
            }
            
            $field_settings = $this->fields[$k];
            $field_instance = $md_field->loadField($field_settings['field_type'], $field_settings, $v);
            $quality_entry[$k] = $field_instance->buildForStorage($v);
            
            //Lib_Gtools_Debug::testLog(__FILE__, $quality_entry, __METHOD__);
        }
        
        $quality_entry['user_id'] = $user_id;
        
        return $model->saveQuality($this->name, $quality_entry);
    }
}