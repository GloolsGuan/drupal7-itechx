<?php

/* 
 * SmartModel业务类型字段声明实例
 * 
 * @Author GloolsGuan<GloolsGuan@163.com>
 * @see \yii\db\ColumnSchema
 * 
 * 字端声明是业务类型字段声明，依赖于SmartModel提供的字段组件，"\Gtools\SmartModle\Coms\Fields\*"
 * 
 * 
 */

return array(
    'FIELD_NAME' => [
        //-- 字段类型,用于系统支持字段类型识别，对应 Coms\Fields\[字端组件名称]，只支持小写 --
        'field_type'=>'username',
        //-- 自定义字段名称，用于Form表单渲染 --
        'field_name' => 'user_name',
        //-- 自定义字段显示Label --
        'label'=>'姓名',
        //-- 字段归类设置 --
        'build_group' => array(),
        //-- 字段值设置回调方法，默认可不设置 --
        'value_callback' => '',
        //-- 字段应用依赖（字段显示依赖） --
        'render_on'=>array(),
        //-- 字段值更新依赖 --
        'update_on' => array(),
        
        /*-- 字段Widget参数配置 --
         * 字段渲染应用插件，通常为默认，对于一些特殊插件，例如时间日期、Gallery，Image等可以
         * 选择渲染插件实现不同的渲染效果，一套渲染插件是一套Html+CSS+JS的结构组合
         */
        'widget' => array()
    ]
);