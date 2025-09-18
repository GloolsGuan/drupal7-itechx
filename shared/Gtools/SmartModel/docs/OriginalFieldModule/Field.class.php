<?php
namespace service\field;

use yii;
use service\base\Base;
use yii\helpers\Html;
use service\field\field\FieldModule;

class Field extends \service\base\Module
{
    public $defaultValue = array();

    public function build($defaultValue){
        $conf = $this->loadConfiguration();
        $html = '';
        $md_field = new FieldModule();
        $i = -1;
        foreach($conf as $field_name=>$field) {
            if(isset($defaultValue[$field_name])){
                $value = $defaultValue[$field_name];
            }else{
                $value = "";
            }
            $html .= $md_field->buildField($field_name, $field, $value);
        }
        return $html;
    }

    public function loadConfiguration(){
        return [
            'config1' => array(
                'field_type'=>'username',
                'label'=>'配置项1',
                'default_value'=> '',
                'build_group' => array(),
                'value_callback' => '',
                'value_arguments' => array(),
                'depend_on'=>array(),
                'related_with' => array(),
                'required' => 'yes',
                'widget' => array(
                    "ajax_submit" => false
                )
            ),
            'config2' => array(
                'field_type'=>'username',
                'label'=>'配置项2',
                'default_value'=> '',
                'build_group' => array(),
                'value_callback' => '',
                'value_arguments' => array(),
                'depend_on'=>array(),
                'related_with' => array(),
                'required' => 'yes',
                'widget' => array(
                    "ajax_submit" => false
                )
            ),
            'config3' => array(
                'field_type'=>'username',
                'label'=>'配置项3',
                'default_value'=> '',
                'build_group' => array(),
                'value_callback' => '',
                'value_arguments' => array(),
                'depend_on'=>array(),
                'related_with' => array(),
                'required' => 'yes',
                'widget' => array(
                    "ajax_submit" => false
                )
            )
        ];
    }
}

