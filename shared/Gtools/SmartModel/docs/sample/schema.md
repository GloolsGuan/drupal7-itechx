## Bundle schema defination ##
```
return array(
    // Name the bundle for programing recognizing, The rule is the same as php variable.
    'name' => '',
    // Human readable name
    'title' => '',
    // Description for note the bundle
    'desc' => '',
    'field_type'=>'user_name',
    'label'=>'姓名',
    'build_group' => array(),
    'value_callback' => 'modules.quality.QualityFieldValues/getValues',
    'value_argument' => 'register_name',
    'depend_on'=>array(),
    'related_with' => array(),
    'widget' => array()
);
```

