<?php

/* 
 * insert into product_loan_types (`type_name`,`parent_id`,`service_for`,`path`)vallues('企业贷',1,'all','1,20'),('全款房贷',1,'all','1,21'),('全款车贷',1,'all','1,22');
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class QualityFieldValues{
    
    public function __construct(){
        parent::__construct('crm_dev');
    }
    
    public function getValues($field_name, $params = array()){
    
        $callback = sprintf('field_%s', $field_name);
        //Lib_Gtools_Debug::testLog(__FILE__, array($callback, $field_name), __METHOD__);
        if (method_exists($this, $callback)) {
            return $this->$callback();
        }

        throw new Exception(sprintf('Invalid field_name "%s" for get default values.', $field_name));
    }
    
    public function field_work_identity(){
        return array(
            array('id'=>1, 'value'=>'个人', 'parent_id'=>0, 'path'=>'1'),
            array('id'=>2, 'value'=>'企业', 'parent_id'=>0, 'path'=>'2'),
            array('id'=>3, 'value'=>'自由职业者', 'parent_id'=>1, 'path'=>'1,3'),
            array('id'=>4, 'value'=>'公务员', 'parent_id'=>1, 'path'=>'1,4'),
            array('id'=>5, 'value'=>'工薪族', 'parent_id'=>1, 'path'=>'1,5'),
            array('id'=>6, 'value'=>'学生', 'parent_id'=>1, 'path'=>'1,6'),
            array('id'=>7, 'value'=>'企业法定代表人', 'parent_id'=>2, 'path'=>'2,7'),
            array('id'=>8, 'value'=>'商户', 'parent_id'=>2, 'path'=>'2,8'),
            array('id'=>9, 'value'=>'企业股东', 'parent_id'=>2, 'path'=>'2,9'),
            array('id'=>10, 'value'=>'上市公司或500强企业员工', 'parent_id'=>1, 'path'=>'1,10'),
        );
    }
    
    
    public function field_industry(){
        return array(
            array('id'=>1, 'value'=>'酒店/餐饮/旅游', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'仓储/物流', 'parent_id'=>0, 'path'=>''),
            array('id'=>3, 'value'=>'教育/培训', 'parent_id'=>0, 'path'=>''),
            array('id'=>4, 'value'=>'房地产/物业 ', 'parent_id'=>0, 'path'=>''),
            array('id'=>5, 'value'=>'建筑/建材', 'parent_id'=>0, 'path'=>''),
            array('id'=>6, 'value'=>'市场/媒介', 'parent_id'=>0, 'path'=>''),
            array('id'=>7, 'value'=>'娱乐/休闲', 'parent_id'=>0, 'path'=>''),
            array('id'=>8, 'value'=>'医疗/护理', 'parent_id'=>0, 'path'=>''),
            array('id'=>9, 'value'=>'计算机/网络', 'parent_id'=>0, 'path'=>''),
            array('id'=>10, 'value'=>'美容/美发', 'parent_id'=>0, 'path'=>''),
            array('id'=>11, 'value'=>'制药/生物', 'parent_id'=>0, 'path'=>''),
            array('id'=>12, 'value'=>'广告/会展', 'parent_id'=>0, 'path'=>''),
            array('id'=>13, 'value'=>'保健按摩', 'parent_id'=>0, 'path'=>''),
            array('id'=>14, 'value'=>'电子电气', 'parent_id'=>0, 'path'=>''),
            array('id'=>15, 'value'=>'美术/设计', 'parent_id'=>0, 'path'=>''),
            array('id'=>16, 'value'=>'运动健身', 'parent_id'=>0, 'path'=>''),
            array('id'=>17, 'value'=>'机械仪器', 'parent_id'=>0, 'path'=>''),
            array('id'=>18, 'value'=>'金融/银行', 'parent_id'=>0, 'path'=>''),
            array('id'=>19, 'value'=>'超市/百货', 'parent_id'=>0, 'path'=>''),
            array('id'=>20, 'value'=>'汽车服务', 'parent_id'=>0, 'path'=>''),
            array('id'=>21, 'value'=>'纺织/食品', 'parent_id'=>0, 'path'=>''),
            array('id'=>22, 'value'=>'交通/运输', 'parent_id'=>0, 'path'=>''),
            array('id'=>23, 'value'=>'农林牧渔 ', 'parent_id'=>0, 'path'=>''),
        );
    }
    
    public function field_fixedasset_type(){
        return array(
            array('id'=>1, 'value'=>'商品房', 'parent_id'=>0, 'path'=>'1'),
            array('id'=>2, 'value'=>'商铺', 'parent_id'=>0, 'path'=>'2'),
            array('id'=>3, 'value'=>'别墅', 'parent_id'=>0, 'path'=>'3'),
            array('id'=>4, 'value'=>'办公楼', 'parent_id'=>0, 'path'=>'4'),
            array('id'=>5, 'value'=>'酒店式公寓', 'parent_id'=>0, 'path'=>'5'),
            array('id'=>6, 'value'=>'经济适用房', 'parent_id'=>0, 'path'=>'6'),
            array('id'=>7, 'value'=>'自建房', 'parent_id'=>0, 'path'=>'7'),
            array('id'=>8, 'value'=>'宅基地', 'parent_id'=>0, 'path'=>'7'),
            array('id'=>9, 'value'=>'其它', 'parent_id'=>0, 'path'=>'7')
        );
    }
    
    public function field_official_level(){
        return array(
            array('id'=>1, 'value'=>'正厅级', 'parent_id'=>0, 'path'=>'1'),
            array('id'=>2, 'value'=>'副厅级', 'parent_id'=>0, 'path'=>'2'),
            array('id'=>3, 'value'=>'正局级', 'parent_id'=>0, 'path'=>'3'),
            array('id'=>4, 'value'=>'副局级', 'parent_id'=>0, 'path'=>'4'),
            array('id'=>5, 'value'=>'正处级', 'parent_id'=>0, 'path'=>'5'),
            array('id'=>6, 'value'=>'副处级', 'parent_id'=>0, 'path'=>'6'),
            array('id'=>7, 'value'=>'职员', 'parent_id'=>0, 'path'=>'7')
        );
    }
    
    public function field_position_level(){
        return array(
            array('id'=>1, 'value'=>'分公司经理', 'parent_id'=>0, 'path'=>'1'),
            array('id'=>2, 'value'=>'部门经理', 'parent_id'=>0, 'path'=>'2'),
            array('id'=>3, 'value'=>'部门主管', 'parent_id'=>0, 'path'=>'3'),
            array('id'=>4, 'value'=>'职员', 'parent_id'=>0, 'path'=>'4')
        );
    }
    
    
    public function field_fixedasset_using_range(){
        return array(
            array('id'=>1, 'value'=>'住宅用地', 'parent_id'=>0, 'path'=>'1'),
            array('id'=>2, 'value'=>'商用土地', 'parent_id'=>0, 'path'=>'2'),
            array('id'=>3, 'value'=>'厂房用地', 'parent_id'=>0, 'path'=>'3'),
            array('id'=>4, 'value'=>'农业用地', 'parent_id'=>0, 'path'=>'4'),
            array('id'=>5, 'value'=>'教育用地', 'parent_id'=>0, 'path'=>'5')
        );
    }
    
    
    public function field_fixedasset_status(){
        return array(
            array('id'=>1, 'value'=>'抵押', 'parent_id'=>0, 'path'=>'1'),
            array('id'=>2, 'value'=>'按揭', 'parent_id'=>0, 'path'=>'2'),
            array('id'=>3, 'value'=>'二押', 'parent_id'=>0, 'path'=>'3'),
            array('id'=>4, 'value'=>'按揭结清一年以内', 'parent_id'=>0, 'path'=>'3'),
            array('id'=>5, 'value'=>'全款', 'parent_id'=>0, 'path'=>'3')
        );
    }
    
    
    public function field_asset_type(){
        return array(
            array('id'=>1, 'value'=>'房产', 'parent_id'=>0, 'path'=>'1'),
            array('id'=>2, 'value'=>'车辆', 'parent_id'=>0, 'path'=>'2'),
            array('id'=>3, 'value'=>'保单', 'parent_id'=>0, 'path'=>'3')
        );
    }
    
    public function field_car_type(){
        return array(
            array('id'=>1, 'value'=>'营运车辆', 'parent_id'=>0, 'path'=>'1'),
            array('id'=>2, 'value'=>'非营运车辆', 'parent_id'=>0, 'path'=>'2')
        );
    }
    
    
    public function field_enterprise_register_date(){
        return array(
            array('id'=>1, 'value'=>'3个月以下', 'parent_id'=>0, 'path'=>'1'),
            array('id'=>2, 'value'=>'3-6个月', 'parent_id'=>0, 'path'=>'2'),
            array('id'=>3, 'value'=>'6-12个月', 'parent_id'=>0, 'path'=>'3'),
            array('id'=>4, 'value'=>'12-18个月', 'parent_id'=>0, 'path'=>'3'),
            array('id'=>5, 'value'=>'18-24个月', 'parent_id'=>0, 'path'=>'3'),
            array('id'=>6, 'value'=>'24个月以上', 'parent_id'=>0, 'path'=>'3')
        );
    }
    
    public function field_loan_type(){
        $st = $this->query('select * from product_loan_types;');
        $loan_types = $st->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($loan_types)) {
            return array();
        }
        
        $lts = array();
        foreach($loan_types as $lt) {
            $lts[$lt['id']] = $lt;
        }
        return $lts;
    }
    
    
    public function field_yes_no(){
        return array(
            array('id'=>1, 'value'=>'是', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'否', 'parent_id'=>0, 'path'=>'')
        );
    }
    
    public function field_ok_no(){
        return array(
            array('id'=>1, 'value'=>'可以', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'不可以', 'parent_id'=>0, 'path'=>'')
        );
    }
    
    public function field_has_no(){
        return array(
            array('id'=>1, 'value'=>'有', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'无', 'parent_id'=>0, 'path'=>'')
        );
    }
    
    public function field_sex(){
        
        return array(
            array('id'=>1, 'value'=>'男士', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'女士', 'parent_id'=>0, 'path'=>'')
        );
    }
    
    public function field_is_marriaged(){
        return array(
            array('id'=>1, 'value'=>'未婚', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'已婚', 'parent_id'=>0, 'path'=>''),
            array('id'=>3, 'value'=>'离异', 'parent_id'=>0, 'path'=>''),
            array('id'=>4, 'value'=>'丧偶', 'parent_id'=>0, 'path'=>'')
        );
    }
    
    
    public function field_asset_statement(){
        return array(
            array('id'=>1, 'value'=>'全款', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'抵押', 'parent_id'=>0, 'path'=>''),
            array('id'=>3, 'value'=>'按揭', 'parent_id'=>0, 'path'=>''),
            array('id'=>4, 'value'=>'按揭结清一年以内', 'parent_id'=>0, 'path'=>''),
        );
    }
    
    
    public function field_credit_status(){
        return array(
            array('id'=>1, 'value'=>'征信无逾期', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'征信有逾期', 'parent_id'=>0, 'path'=>''),
            array('id'=>3, 'value'=>'白户', 'parent_id'=>0, 'path'=>''),
            array('id'=>4, 'value'=>'黑户', 'parent_id'=>0, 'path'=>''),
        );
    }
    
    
    public function field_guarantee_type(){
        return array(
            array('id'=>1, 'value'=>'寿险', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'非寿险', 'parent_id'=>0, 'path'=>'')
        );
    }
    
    
    public function field_guarantee_org(){
        return array(
            array('id'=>1, 'value'=>'中国人寿', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'平安人寿', 'parent_id'=>0, 'path'=>''),
            array('id'=>3, 'value'=>'太平洋人寿', 'parent_id'=>0, 'path'=>''),
            array('id'=>3, 'value'=>'泰康人寿', 'parent_id'=>0, 'path'=>'')
        );
    }
    
    
    public function field_company_type(){
        return array(
            array('id'=>1, 'value'=>'商户', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'中小微企业', 'parent_id'=>0, 'path'=>''),
            array('id'=>3, 'value'=>'股份制公司', 'parent_id'=>0, 'path'=>''),
            array('id'=>4, 'value'=>'国企', 'parent_id'=>0, 'path'=>''),
            array('id'=>5, 'value'=>'上市公司', 'parent_id'=>0, 'path'=>'')
        );
    }
    
    
    public function field_graduated_type(){
        return array(
            array('id'=>1, 'value'=>'研究生及以上', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'本科', 'parent_id'=>0, 'path'=>''),
            array('id'=>3, 'value'=>'专科', 'parent_id'=>0, 'path'=>''),
            array('id'=>4, 'value'=>'高中及以下', 'parent_id'=>0, 'path'=>'')
        );
    }




    public function field_grant_salary_mode(){
        return array(
            array('id'=>1, 'value'=>'打卡工资', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'自存工资', 'parent_id'=>0, 'path'=>''),
            array('id'=>3, 'value'=>'现金发放', 'parent_id'=>0, 'path'=>'')
        );
    }
    
    public function field_house_type(){
        return array(
            array('id'=>1, 'value'=>'商品房', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'经济适用房', 'parent_id'=>0, 'path'=>''),
            array('id'=>3, 'value'=>'房改房', 'parent_id'=>0, 'path'=>''),
            array('id'=>4, 'value'=>'别墅', 'parent_id'=>0, 'path'=>''),
            array('id'=>5, 'value'=>'商铺', 'parent_id'=>0, 'path'=>''),
            array('id'=>6, 'value'=>'酒店式公寓', 'parent_id'=>0, 'path'=>''),
            array('id'=>7, 'value'=>'其它', 'parent_id'=>0, 'path'=>''),
        );
    }
    
    public function field_debt_type(){
        return array(
            array('id'=>1, 'value'=>'抵押贷款', 'parent_id'=>0, 'path'=>''),
            array('id'=>2, 'value'=>'信用贷款', 'parent_id'=>0, 'path'=>''),
            array('id'=>3, 'value'=>'担保贷款', 'parent_id'=>0, 'path'=>''),
        );
    }
    
    public function field_bank_type(){
        return array(
            array('id'=>1, 'value'=>'银行', 'parent_id'=>0, 'path'=>'1','provider'=>'bank'),
            array('id'=>2, 'value'=>'小贷公司', 'parent_id'=>0, 'path'=>'2','provider'=>'company'),
            array('id'=>3, 'value'=>'p2p网贷', 'parent_id'=>0, 'path'=>'3','provider'=>'p2p'),
            array('id'=>4, 'value'=>'投资公司', 'parent_id'=>0, 'path'=>'4','provider'=>'invest'),
            array('id'=>6, 'value'=>'典当行', 'parent_id'=>0, 'path'=>'6','provider'=>'pawn'),
            array('id'=>12, 'value'=>'担保公司', 'parent_id'=>0, 'path'=>'12','provider'=>'warrant'),
            array('id'=>5, 'value'=>'其他资金', 'parent_id'=>0, 'path'=>'5','provider'=>'other'),
        );
    }
    
    public function field_product_action_type(){
    	return array(
    	    '0' => '等待审核',
    	    '1' => '等待总部审核',
	    	'2' => '审核成功',
	    	'3' => '审核失败',
    	);
    }
    
    public function field_product_approve_type(){
    	return array(
    	    'passed' => '审核通过',
    	    'unpassed' => '审核不通过',
	    	'caceled' => '取消审核',
    	);
    }
    
    public function field_address(){
        Yii::import('modules.common.AreasModule');
        static $cities = array();
        
        $zones = AreasModule::loadAreas(0);
        
        if (!empty($cities)) {
            return $cities;
        }
        
        foreach($zones as $z) {
            $cities[$z['id']] = $z;
        }
        
        //Lib_Gtools_Debug::testLog(__FILE__, $cities, __METHOD__);
        
        return $cities;
    }
}




 

