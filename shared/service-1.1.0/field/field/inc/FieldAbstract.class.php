<?php
namespace service\field\field\inc;

abstract class FieldAbstract
{
    public $field_name = null;
    public $label = '未定义';
    public $field_type = '';
    public $weight = 0;
    public $build_group = array('name'=>'', 'weight'=>'', 'description'=>'');
    public $value_callback = '';
    public $value_arguments = array();
    //-- Field default value --
    public $default_value = null;
    public $validator = array();
    public $multiple_values = false;
    public $desc = '';
    public $storage = array();
    public $widget  = array();
    //-- Optional values from presettings --
    public $values  = array();
    public $required= array();
    
    public $feature = 'default';
    public $buildContent   = '';
    
    public $depend_on = array(
        'field_name' => array('rule'=>'any', 'value'=>'any')
    );
    
    public $related_with = array(
        'field_name' => 'optional' // allowed values: optional(A ? B), all(A and B), alternative (A or B)
    );
    
    protected static $fields_conf=null;
    

    public function __construct($field_name = '',$field_settings = array(),  $parent = null) {
//        $this->field_name = $field_name;
//        $this->label = $field_settings['label'];
//        $this->field_type = $field_settings['field_type'];
//        $this->widget = $field_settings['widget'];
//        $this->build_group = $field_settings['build_group'];

        $field_type = $field_settings['field_type'];

        if (empty($field_name)) {
            throw new Exception('Feild name is required by ' . __METHOD__);
        }


        //Lib_Gtools_Debug::testLog(__FILE__, $field_name, __METHOD__);
        $field_settings['field_name'] = $field_name;

        $field_settings = self::mergeArray(self::$fields_conf[$field_type], $field_settings);

        //Lib_Gtools_Debug::testLog(__FILE__, $field_settings['widget'], __METHOD__);
//        parent::__construct($field_name, $parent, $field_settings);
        $this->cons($field_name, $parent, $field_settings);

//        print_r($this->default_value);
//        exit;
    }

    function cons($field_name, $parent, $field_settings){
        foreach ($field_settings as $k => $v){
            $this->$k = $v;
        }
    }

    public function setStorage($field_storage){
        return $field_storage;
    }
    
    public function setWidget($widget_settings){
        $widget_settings['ajax_submit'] = false;
        return $widget_settings;
    }
    
    public function buildForStorage($value){
        return $value;
    }
    
    //-- Build field --
    
    /**
     * Build field for render
     */
    public function build($field_type){
        $this->loadValues();
        $this->beforeBuild();
        $tpl_path = sprintf(dirname(__FILE__) . '/../templates/field_%s_%s.tpl.php', $field_type, $this->feature);
        ob_start();
        include($tpl_path);
        $this->buildContent = ob_get_clean();
        $this->afterBuild();
        
        return $this->buildContent ;
    }
    
    
    public function beforeBuild(){}
    public function afterBuild(){}
    
    
    public function buildText($value_text=''){
        
        $prefix = empty($this->widget['prefix']) ? '' : $this->widget['prefix'];
        $suffix = empty($this->widget['suffix']) ? '' : $this->widget['suffix'];
        $value_text = empty($value_text) ? $this->parseValueForBuildText() : $value_text;
        //Lib_Gtools_Debug::testLog(__FILE__, $value_text, __METHOD__);
        return sprintf('<span class="label-title">%s</span><span class="label-prefix">%s</span><span class="label-value">%s</span><span class="label-suffix">%s</span>', $this->label, $prefix, $value_text, $suffix);
    }
    
    public function parseValueForBuildText(){
        $this->loadValues();
        $form_value = $this->default_value;
        //Lib_Gtools_Debug::testLog(__FILE__, $form_value, __METHOD__);
        if (!is_array($form_value) && empty($this->values)) {
            return $form_value;
        } else if (!is_array($form_value) && !empty($this->values)) {
            return $this->values[$form_value]['value'];
        } else if (is_array($form_value) && !empty($this->values) && !array_key_exists('rule', $form_value)) {
            $re = array();
            foreach($form_value as $id) {
                $re[] = $this->values[$id]['value'];
            }
            
            return implode(',', $re);
        } else if (is_array($form_value) && array_key_exists('rule', $form_value) && in_array($form_value['rule'], array('*', '~'))) {
            $re = '';
            if (!empty($form_value['min']) && !empty($form_value['max'])) {
                if ('*'==$form_value['rule']) {
                    return sprintf('从%s 到(并包含) %s', $form_value['min'], $form_value['max']);
                } else {
                    return sprintf('从%s 到(不包含边界) %s', $form_value['min'], $form_value['max']);
                }
            } else if (!empty($form_value['min'])){
                if ('*'==$form_value['rule']) {
                    return sprintf('大于（并包含）%s', $form_value['min']);
                } else {
                    return sprintf('大于 %s', $form_value['min']);
                }
            } else if (!empty($form_value['max'])){
                if ('*'==$form_value['rule']) {
                    return sprintf('小于（并包含）%s', $form_value['max']);
                } else {
                    return sprintf('小于 %s', $form_value['max']);
                }
            }
        }
        
        
        return json_encode($form_value);
    }
    
    
    public function loadValues(){
        if (empty($this->value_callback)) {
            return null;
        }
        
        $path_args = explode('/', $this->value_callback);
        $path = $path_args[0];
        
        $class = substr($path, strrpos($path, '.')+1);
        $method = $path_args[1];
        
        $name = $this->value_arguments[0];
        $args = array();
        
        if (!empty($this->value_arguments)) {
            $args = $this->value_arguments;
        }

        Yii::import($path);
        $value_hook = new $class();
        $values = $value_hook->$method($name, $args);
        
        $vp = array();
        $ignore = array();
        if (!in_array($name, $ignore)) {
            foreach($values as $value) {
                $vp[$value['id']] = $value;
            }
            $this->values = $vp;
        } else {
            $this->values = $values;
        }
    }
    
    public function ParseElement($tpl,$arr)
    {
        return preg_replace_callback("/{(\\w+)}/i",function($m) use($arr){
            return $arr[$m[1]];
        },$tpl);
    }

    public static function mergeArray($a,$b)
    {
        $args=func_get_args();
        $res=array_shift($args);
        while(!empty($args))
        {
            $next=array_shift($args);
            foreach($next as $k => $v)
            {
                if(is_integer($k))
                    isset($res[$k]) ? $res[]=$v : $res[$k]=$v;
                elseif(is_array($v) && isset($res[$k]) && is_array($res[$k]))
                    $res[$k]=self::mergeArray($res[$k],$v);
                else
                    $res[$k]=$v;
            }
        }
        return $res;
    }
}
    
  