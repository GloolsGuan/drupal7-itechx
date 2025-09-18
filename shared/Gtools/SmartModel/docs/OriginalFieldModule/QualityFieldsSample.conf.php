<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return array(
    'label' => '个人身份',
    'name'	=> 'person',
    'depend_on' => array(),
    'category'=>'基本要求',
    'fields' => array(
        'register_name' => array(
            'field_type'=>'user_name',
            'label'=>'姓名',
            'build_group' => array(),
            'value_callback' => 'modules.quality.QualityFieldValues/getValues',
            'value_argument' => 'register_name',
            'depend_on'=>array(),
            'related_with' => array(),
            'widget' => array()
        ),
        'id_card' => array(
            'field_type'=>'id_card',
            'label'=>'身份证号码',
            'build_group' => array(),
            'value_callback' => 'modules.quality.QualityFieldValues/getValues',
            'value_argument' => 'id_card',
            'depend_on'=>array(),
            'related_with' => array(),
            'widget' => array()
        ),
        'sex' => array(
            'field_type'=>'checkbox',
            'label'=>'性别',
            'build_group' => array(),
            'value_callback' => 'modules.quality.QualityFieldValues/getValues',
            'value_argument' => 'sex',
            'depend_on'=>array(),
            'related_with' => array(),
            'widget' => array()
        ),
        'is_married' => array(
            'field_type'=>'checkbox',
            'label'=>'是否结婚',
            'build_group' => array(),
            'value_callback' => 'modules.quality.QualityFieldValues/getValues',
            'value_argument' => 'work_identity',
            'depend_on'=>array(),
            'related_with' => array(),
            'widget' => array()
        ),
        'living_place' => array(
            'field_type'=>'address',
            'label'=>'居住地',
            'build_group' => array(),
            'value_callback' => 'modules.quality.QualityFieldValues/getValues',
            'value_argument' => 'work_identity',
            'depend_on'=>array(),
            'related_with' => array(),
            'widget' => array()
        ),
        'register_place' => array(
            'field_type'=>'address',
            'label'=>'户籍所在地',
            'build_group' => array(),
            'value_callback' => 'modules.quality.QualityFieldValues/getValues',
            'value_argument' => 'work_identity',
            'depend_on'=>array(),
            'related_with' => array(),
            'widget' => array()
        ),
        'work_identity' => array(
            'field_type'=>'category',
            'label'=>'职业身份',
            'build_group' => array(),
            'value_callback' => 'modules.quality.QualityFieldValues/getValues',
            'value_argument' => 'work_identity',
            'depend_on'=>array(),
            'related_with' => array(),
            'widget' => array()
        )
    )
);