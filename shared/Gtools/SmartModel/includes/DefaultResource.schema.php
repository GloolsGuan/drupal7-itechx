<?php

/* 
 * Default bundle structure defination
 */
return array(
    //-- Bundle name rule is the same as PHP variable --
    'name' => 'default_resource_name',
    //-- Human readable title max 256 character --
    'title' => 'Default bundle',
    //-- Short description of the bundle --
    'brief' => 'You should update these information to your local bundle information',
    // -- Bundle basic field structure, It is forbidden for updating.
    'fields' => [
        'id' => [
            //-- 
            'field_type'=>'username',
            'label'=>'姓名',
            'build_group' => array(),
            'value_callback' => 'modules.quality.QualityFieldValues/getValues',
            'value_argument' => 'register_name',
            'depend_on'=>array(),
            'related_with' => array(),
            'widget' => array()
        ],
        'type' => [
            
        ],
        'unique_code' => [],
        'title' => [],
        'owner' => [],
        'created_at' => [],
        'updated_at' => [],
        'status' => [],
        'parent_id' => [],
        'xpath' => [],
    ]
);

