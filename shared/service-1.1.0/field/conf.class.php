<?php
namespace app\widgets\field;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Conf{
    static function conf(){
        return array(
            'label' => '个人基本信息',
            'name'	=> 'person',
            'depend_on' => array(),
            'category'=>'基本',
            'fields' => array(
                //-- group: basic information --
                'title' => array(
                    'field_type'=>'username',
                    'label'=>'标题',
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
                'sex' => array(
                    'field_type'=>'checkbox',
                    'label'=>'性别',
                    'build_group' => array(),
                    'values' => array("1"=>["value"=>"男"],"2"=>["value"=>"女"]),
                    'value_callback' => '',
                    'value_arguments' => array('sex'),
                    'depend_on'=>array(),
                    'related_with' => array(),
                    'required' => 'yes',
                    'widget' => array(
                        'type' => 'radio',
                        "ajax_submit" => false
                    )
                ),
                'id_card' => array(
                    'field_type'=>'idcard',
                    'label'=>'身份证号码',
                    'build_group' => array(),
                    'value_callback' => '',
                    'value_arguments' => array(),
                    'depend_on'=>array(),
                    'related_with' => array(),
                    'widget' => array()
                ),
                'birthday' => array(
                    'field_type'=>'datetime',
                    'label'=>'出生年份',
                    'build_group' => array(),
                    'value_callback' => '',
                    'value_arguments' => array(),
                    'depend_on'=>array(),
                    'related_with' => array(),
                    'required' => 'yes',
                    'widget' => array()
                ),
                'age' => array(
                    'field_type'=>'numeric',
                    'label'=>'年龄',
                    'build_group' => array(),
                    'value_callback' => '',
                    'value_arguments' => array(),
                    'depend_on'=>array(),
                    'related_with' => array(),
                    'required' => 'yes',
                    'widget' => array(
                        'suffix' => '周岁'
                    )
                ),
                'mobile' => array(
                    'field_type'=>'mobile',
                    'label'=>'手机号码',
                    'build_group' => array(),
                    'value_callback' => '',
                    'value_arguments' => array(),
                    'depend_on'=>array(),
                    'related_with' => array(),
                    'required' => 'yes',
                    'widget' => array()
                ),
                'is_marriaged' => array(
                    'field_type'=>'checkbox',
                    'label'=>'婚姻状况',
                    'build_group' => array(),
                    'value_callback' => 'modules.quality.QualityFieldValues/getValues',
                    'value_arguments' => array('is_marriaged'),
                    'depend_on'=>array(),
                    'related_with' => array(),
                    'widget' => array(
                        'type' => 'radio'
                    )
                ),
                'graduated' => array(
                    'field_type'=>'checkbox',
                    'label'=>'学历',
                    'build_group' => array(),
                    'value_callback' => 'modules.quality.QualityFieldValues/getValues',
                    'value_arguments' => array('graduated_type'),
                    'depend_on'=>array(),
                    'related_with' => array(),
                    'widget' => array(
                        'type' => 'radio'
                    )
                ),
                'living_place' => array(
                    'field_type'=>'address',
                    'label'=>'现居住地',
                    'build_group' => array(),
                    'value_callback' => 'modules.quality.QualityFieldValues/getValues',
                    'value_arguments' => array('address'),
                    'depend_on'=>array(),
                    'related_with' => array(),
                    'widget' => array()
                ),
                'register_place' => array(
                    'field_type'=>'address',
                    'label'=>'户籍所在地',
                    'build_group' => array(),
                    'value_callback' => 'modules.quality.QualityFieldValues/getValues',
                    'value_arguments' => array('address'),
                    'depend_on'=>array(),
                    'related_with' => array(),
                    'widget' => array()
                ),
                'work_identity' => array(
                    'field_type'=>'category',
                    'label'=>'职业身份',
                    'build_group' => array('name'=>'职业', 'weight'=>99, 'description'=>'职业相关信息'),
                    'value_callback' => 'modules.quality.QualityFieldValues/getValues',
                    'value_arguments' => array('work_identity'),
                    'depend_on'=>array(),
                    'related_with' => array(),
                    'widget' => array()
                ),
                'official_level' => array(
                    'field_type'=>'category',
                    'label'=>'职级',
                    'build_group' => array(),
                    'value_callback' => 'modules.quality.QualityFieldValues/getValues',
                    'value_arguments' => array('official_level'),
                    'depend_on'=>array('work_identity'=>array('rule'=>'=', 'value'=>4)),
                    'related_with' => array(),
                    'widget' => array()
                ),
                'school' => array(
                    'field_type'=>'school',
                    'label'=>'学校',
                    'build_group' => array(),
                    'value_callback' => '',
                    'value_arguments' => array(),
                    'depend_on'=>array('work_identity'=>array('rule'=>'=', 'value'=>'6')),
                    'related_with' => array(),
                    'widget' => array()
                ),
                'family_member_name' => array(
                    'field_type'=>'username',
                    'label'=>'父亲/母亲姓名',
                    'build_group' => array(),
                    'value_callback' => '',
                    'value_arguments' => array(),
                    'depend_on'=>array('work_identity'=>array('rule'=>'=', 'value'=>'6')),
                    'related_with' => array(),
                    'widget' => array()
                ),
                'family_member_idcard' => array(
                    'field_type'=>'idcard',
                    'label'=>'父亲/母亲身份证号码',
                    'build_group' => array(),
                    'value_callback' => '',
                    'desc' => '需与父亲/母亲姓名保持一致。',
                    'value_arguments' => array(),
                    'depend_on'=>array('work_identity'=>array('rule'=>'=', 'value'=>'6')),
                    'related_with' => array(),
                    'widget' => array()
                ),
                'stock_share' => array(
                    'field_type'=>'numeric',
                    'label'=>'持股比例',
                    'build_group' => array(),
                    'value_callback' => '',
                    'value_arguments' => array(),
                    'depend_on'=>array('work_identity'=>array('rule'=>'=', 'value'=>9)),
                    'related_with' => array(),
                    'widget' => array(
                        'suffix' => '%'
                    )
                ),
                'grant_salary_mode' => array(
                    'field_type'=>'category',
                    'label'=>'薪资发放形式',
                    'weight' => 98,
                    'build_group' => array('name'=>'职业'),
                    'value_callback' => 'modules.quality.QualityFieldValues/getValues',
                    'value_arguments' => array('grant_salary_mode'),
                    'depend_on'=>array('stock_share'=>array('rule'=>'<', 'value'=>20), 'work_identity'=>array('rule'=>'in', 'value'=>'1,4,5,10')),
                    'related_with' => array(),
                    'widget' => array()
                ),
                'freelancer_yearly_income' => array(
                    'field_type'=>'numeric',
                    'label'=>'年收入',
                    'weight' => 99,
                    'build_group' => array('name'=>'职业'),
                    'value_callback' => '',
                    'value_arguments' => array(),
                    'depend_on'=>array('work_identity'=>array('rule'=>'=', 'value'=>'3')),
                    'related_with' => array('grant_salary_mode'=>'all'),
                    'widget' => array(
                        'suffix' => '元'
                    )
                ),
                'month_salary' => array(
                    'field_type'=>'numeric',
                    'label'=>'月薪',
                    'weight' => 99,
                    'build_group' => array('name'=>'职业'),
                    'value_callback' => '',
                    'value_arguments' => array(),
                    'depend_on'=>array('stock_share'=>array('rule'=>'<', 'value'=>20), 'work_identity'=>array('rule'=>'in', 'value'=>'1,4,5,10')),
                    'related_with' => array('grant_salary_mode'=>'all'),
                    'widget' => array(
                        'suffix' => '元'
                    )
                ),
                'months_of_social_fund' => array(
                    'field_type'=>'numeric',
                    'label'=>'社保缴纳时长',
                    'weight' => 99,
                    'build_group' => array('name'=>'职业'),
                    'value_callback' => '',
                    'value_arguments' => array(),
                    'depend_on'=>array('month_salary'=>array('rule'=>'>', 'value'=>2000)),
                    'related_with' => array(),
                    'widget' => array(
                        'suffix' => '月'
                    )
                ),
                'months_of_house_fund' => array(
                    'field_type'=>'numeric',
                    'label'=>'公积金缴纳时长',
                    'weight' => 99,
                    'build_group' => array('name'=>'职业'),
                    'value_callback' => '',
                    'value_arguments' => array(),
                    'depend_on'=>array('months_of_social_fund'=>array('rule'=>'>', 'value'=>6)),
                    'related_with' => array(),
                    'widget' => array(
                        'suffix' => '月'
                    )
                ),
                'monthly_pay_of_house_fund' => array(
                    'field_type'=>'numeric',
                    'label'=>'公积金交存基数',
                    'weight' => 99,
                    'build_group' => array('name'=>'职业'),
                    'value_callback' => 'modules.quality.QualityFieldValues/getValues',
                    'value_arguments' => array('has_no'),
                    'depend_on'=>array('work_identity'=>array('rule'=>'in', 'value'=>'1,4,5,10')),
                    'related_with' => array(),
                    'widget' => array(
                        'suffix'=>'元/月'
                    )
                )
            )
        );
    }
}

