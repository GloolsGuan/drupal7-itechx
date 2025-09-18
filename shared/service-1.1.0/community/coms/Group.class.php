<?php
namespace service\community\coms;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Group extends \service\base\Component{
    
    
    public function __construct($id = '', $parent = null, $config = array()) {
        parent::__construct($id, $parent, $config);
    }
    
    
    public function init(){
        if (empty($this->base_entity)) {
            throw new \Exception('System error: Failed to initialize service\community[Group] component, Base_entity is required.');
        }
        
        $this->base_entity_id = $this->base_entity['id'];
    }
    
    
    public function loadGroup($group_id){
        
    }
    
    
    public function loadGroupsByType($group_type='', $status='active'){
        $model_group = new \service\community\models\Group();
        //\GtoolsDebug::testLog(__METHOD__, [$this->base_entity, $this->base_entity_id, $this->base_name]);
        $groups = $model_group->find()->where(['community_id'=>$this->base_entity_id, 'type'=>$group_type, 'status'=>$status])->asArray()->all();
        
        if (null==$groups) {
            return $this->buildResponse('success', 299, null);
        }
        
        return $this->buildResponse('success', 201, $groups);
    }
    
    
    public function loadGroups($page=0, $status='active', $rows_per_page=20){
        $rows = $rows_per_page;
        $model_group = new \service\community\models\Group();
        $query = $model_group->find()->where(['community_id'=>$this->base_entity['id'], 'status'=>$status]);
        
        if (is_numeric($page)&&$page>0){
            $start_point = ($page-1)*$row_per_page;
            $total = $query->select(['count(id)'])->asArray()->one();
            \GtoolsDebug::testLog(__METHOD__, $total);
            $query->limit($start_point, $row_per_page);
        }
        
        $groups = $query->asArray()->all();
        if (null==$groups) {
            return $this->buildResponse('success', 299, null);
        }
        
        return $this->buildResponse('success', 201, $groups);
    }
    
    
    /**
     * Statistics for group
     * 
     */
    public function loadStatistic(){
        
    }
    
    
    /**
     * 
     * Please @see models\Group for Attributes of $group_entity_data
     * 
     * @param type $group_entity_data
     * @param type $operator_uc_code
     */
    public function createGroup($group_entity_data, $operator_uc_code){
        $model_group = new \service\community\models\Group();
        
        $model_group->setAttribute('operator_uc_code', $operator_uc_code);
        $model_group->setAttributes($group_entity_data)->validate();
        if ($model_group->hasErrors()) {
            return $this->buildResponse('error', 400, $this->getErrors());
        }
        
        $result = $model_group->insert();
        if (null==$result) {
            return $this->buildResponse('failed', 500, $model_group->getErrors('db.insert'));
        }
        
        //\GtoolsDebug::testLog(__METHOD__, $result);
        $this->addMember($result['id'], $result['creator_uc_code'], 0, [
            'participation_type' => 'founder'
        ]);
        
        return $this->buildResponse('success', 201, $result);
    }
    
    
    public function updateGroup($group_id, $group_entity){
        
    }
    
    
    public function removeGroup($group_id, $operator_code){
        
    }
    
    
    public function loadGroupsForMember($member_id){
        
    }
    
    
    public function addMember($group_id, $uc_user_code, $role_id=0, $settings=[]){
        
        $member_entity = array_merge($settings, ['group_id'=>$group_id, 'uc_user_code'=>$uc_user_code, 'role_id'=>$role_id]);
        //\GtoolsDebug::testLog(__METHOD__, $member_entity);
        $model_member = new \service\community\models\Member();
        $model_member->setAttributes($member_entity)->validate();
        
        if ($model_member->hasErrors()){
            return $this->buildResponse('error', 401, $model_member->getErrors());
        }
        
        
        if($model_member->find()->where(['group_id'=>$group_id, 'uc_user_code'=>$uc_user_code])->one()) {
            return $this->buildResponse('error', 402, 'The member has been existed.');
        }
        
        $member_result = $model_member->insert();
        if (null==$member_result) {
            return $this->buildResponse('failed', 500, $model_member->getErrors('db.insert'));
        }
        
        return $this->buildResponse('success', 201, $member_result);
    }
    
    
    public function removeMember($group_id, $member_id){
        
    }
    
    
    public function lockMember($group_id, $member_id){
        
    }
    
    
    public function updateMember($group_id, $member_id, $settings){
        
    }
    
}