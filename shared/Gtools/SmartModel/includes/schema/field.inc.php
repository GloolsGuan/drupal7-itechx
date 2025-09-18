<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return [
    'Field Name' => [
        //-- 
        'field_type'=>'username',
        'label'=>'姓名',
        'build_group' => array(),
        'value_callback' => 'modules.quality.QualityFieldValues/getValues',
        'value_argument' => 'register_name',
        'depend_on'=>array(
            ['field'=>'xxx', 'condition'=>'>', 'value'=>100],
            ['field'=>'yyy', 'rule'=>'', 'value'=>'']
        ),
        'related_with' => array(),
        'widget' => array()
    ]
];