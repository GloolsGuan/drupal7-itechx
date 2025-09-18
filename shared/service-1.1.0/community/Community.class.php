<?php
namespace service\community;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use libs\Groups;

class Community extends \service\base\Module{
    
    /**
     * Build an community:
     * > Build an community.
     * > Build an public group for community.
     * > Add creator of the group as the first member of the group.
     * 
     * @param array $community_entity_data The community entity data for build community.
     * attributes of $community_entity_data:
     * - [required] "title" (string) Community title
     * - "type" (string:100) community type, defined by community creator or leave it empty.
     * - [required] "master_title" (string:255) Readable master title information, Generally the title used to build master_code value, e.g:\service\plan\Plan .
     * - [required] "master_code" (string:64) The coded community identity for master, e.g: hash('sha256', '\service\plan\Plan'), It is means the
     *   community is build for plan/123.
     * - [required] "master_ext_id" The master defined relationship id, e.g: For master plan, the master_ext_id should be 123 or any plan_id.
     * - [required] "founder_uc_code" string(64) who is the owner of the community founder, e.g: for plan, The value should be the same with master plan.
     * - "logo" string， An file path.
     * - "intro" community introduction.
     */
    public function build($community_entity_data, $operator_uc_code=''){
        
        $model_community = new models\Community();
        $model_community->setAttributes($community_entity_data)->validate();

        if ($model_community->hasErrors()){
            return $this->buildResponse('error', 401, $model_community->getErrors());
        }
        
        if ($this->isExisted($model_community)) {
            $error_mes = sprintf( 'The community has existed. It is means the unique parameters "master_code:%s,master_ext_id:%s"has been exists.', $community_entity_data['master_code'], $community_entity_data['master_ext_id']);
            return $this->buildResponse('error', 402,$error_mes);
        }
        
        if (!$this->validMembers([$model_community->founder_uc_code, $operator_uc_code])) {
            return $this->buildResponse('error', 403, $this->getErrors('valid_members'));
        }
        $model_community->setAttribute('operator_uc_code', $operator_uc_code);
        
        
        //-- [Step: 1] Create community --
        $community_entity_data = $model_community->insert();
        if (null==$community_entity_data) {
            return $this->buildResponse('failed', 501, $model_community->getErrors('db.insert'));
        }
        //\GtoolsDebug::testLog(__METHOD__, $community_entity_data);
        $scom_group = $this->loadCom('group', $community_entity_data);
        //-- [Step: 2] Create default group for community --
        $group_entity_data = $this->buildDefaultGroupEntity($community_entity_data);
        $group_entity_result = $scom_group->createGroup($group_entity_data, $operator_uc_code);
        if (201!==$group_entity_result['code']) {
            $model_community->find()->where(['id'=>$community_entity_data['id']])->delete();
            return $this->buildResponse('failed', 502, $group_entity_result['data']);
        }
        
        //-- [Step: 3] Add founder as the default member of the 'default_group' --
        /*
        $group_entity_data = $group_entity_result['data'];
        $member_entity_result = $scom_group->addMember($group_entity_data['id'], $community_entity_data['founder_uc_code'], 0, [
            'participation_type' => 'founder'
        ]);
        */
        
        return $this->buildResponse('success', 201, $community_entity_data);
    }
    
    public function load($community_id){
        $model_community = new models\Community();
        $community = $model_community->find()->where(['id'=>$community_id])->one();
        if (!empty($community)) {
            return $this->buildResponse('success', 201, $community->getAttributes());
        }
        
        return $this->buildResponse('success', 299, null);
    }
    
    public function loadByMasterExtId($master_code, $master_ext_id){
        $model_community = new models\Community();
        $community = $model_community->find()->where(['master_code'=>$master_code, 'master_ext_id'=>$master_ext_id])->one();
        if (!empty($community)) {
            return $this->buildResponse('success', 201, $community->getAttributes());
        }
        
        return $this->buildResponse('success', 299, null);
    }
    
    
    public function loadBaseEntity($entity_id){
        $model_community = new models\Community();
        $community = $model_community->find()->where(['id'=>$entity_id])->one();
        if (empty($community)) {
            return $this->buildResponse('success', 200, null);
        }
        
        return $this->buildResponse('success', 201, $community);
    }
    
    
    public function update(){
        
    }
    
    
    public function remove(){
        
    }
    
    public function lock(){
        
    }
    
    protected function buildDefaultGroupEntity($community_entity_data){
        return [
            'community_id' => $community_entity_data['id'],
            'title' => 'default_group',
            'type' => 'public',
            'creator_uc_code' => $community_entity_data['founder_uc_code']
        ];
    }
    
    
    protected function isExisted($model_community){
        return false;
    }
    
    
    protected function validMembers($members){
        return true;
    }
}