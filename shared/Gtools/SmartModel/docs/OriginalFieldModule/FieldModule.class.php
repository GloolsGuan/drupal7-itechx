<?php
namespace service\field\field;

use yii;
use service\field\field\inc\FieldAbstract;

class FieldModule
{

    public function buildField($field_name, $field_schema, $value=null){
        //Lib_Gtools_Debug::testLog(__FILE__, $field_schema, __METHOD__);
        $field_class = sprintf('Field%sModule', ucfirst($field_schema['field_type']));
        $path = sprintf('modules.field.%s', $field_class);
        //Lib_Gtools_Debug::testLog(__FILE__, array($field_name, $field_schema), __METHOD__);
        try{
            $path = $field_class.".class.php";
            $ns = 'service\field\field\\'.$field_class;
            $this->import($ns);
//            $md_field = new $field_class($field_name, $field_schema);
            $md_field = $this->loadField($field_name,$field_schema,$value);
            if ($md_field instanceof FieldAbstract) {
                if (!empty($value)) {
                    $md_field->default_value = $value;
                }
                return $md_field->build($field_schema['field_type']);
            }
        }  catch (Exception $e) {
            throw $e;
        }
    }

    public function loadField($field_name, $field_schema, $value=null){
        $field_class = sprintf('Field%sModule', ucfirst($field_schema['field_type']));
        $path = sprintf('modules.field.%s', $field_class);
        $ns = 'service\field\field\\'.$field_class;
        $this->import($ns);
        try{
            $md_field = new $field_class($field_name, $field_schema);
            //Lib_Gtools_Debug::testLog(__FILE__, $md_field, __METHOD__);
            $md_field->default_value = $value;

            if ($md_field instanceof FieldAbstract) {
                return $md_field;
            }

        }  catch (Exception $e) {
            throw $e;
        }
    }


    public function import($namespace)
    {
        static $registered = false;
        static $paths = [];
        static $classMap = [];

        if (!$registered) {
            spl_autoload_register(function($class) use(&$paths, &$classMap) {
                if (empty($paths) && empty($classMap)) {
                    return;
                }
                if (strpos($class, '\\') === false) {
                    if (isset($classMap[$class])) {
                        return class_alias($classMap[$class], $class);
                    } else {
                        $baseFile = '/' . str_replace('_', '/', $class) . '.php';
                        foreach ($paths as $namespace => $path) {
                            if (is_file($path . $baseFile)) {
                                return class_alias($namespace . '\\' . $class, $class);
                            }
                        }
                    }
                }
            });
            $registered = true;
        }
        if (($pos = strrpos($namespace, '\\')) !== false) {
            $ns = trim(substr($namespace, 0, $pos), '\\');
            $alias = substr($namespace, $pos + 1);
            if ($alias === '*') {
                if (!isset($paths[$ns]) || $paths[$ns] === false) {
                    $paths[$ns] = Yii::getAlias('@' . str_replace('\\', '/', $ns), false);
                }
            } elseif (!empty($alias)) {
                $classMap[$alias] = trim($namespace, '\\');
            }
        } else {
            throw new yiibaseInvalidParamException("Invalid import alias: $namespace");
        }
    }

}
    
  