<?php

namespace service\plan;
 
use \service\base\Module;
use \service\plan\models\Participant as ModParticipant;
use service\plan\models\Trainee as ModTrainee;
use \service\plan\models\RsTraineeGroup as ModRsTraineeGroup;
use \service\plan\interfaces\Group as GroupInterfaces;

class Group extends Module implements GroupInterfaces
{
    /**
     * 将学员加入小组，学员不能同时存在于两个小组
     *
     * @param int $groupId 组id
     * @param int $participantId 学员id
     * @return array 成功时返回true
     * @author drce 20161129
     */
    public function addMember($groupId, $participantId)
    {
        if (empty($groupId)) return $this->buildResponse('error', 400, '$groupId cannot be empty');
        if (empty($participantId)) return $this->buildResponse('error', 400, '$participantId cannot be empty');
        
        if (NULL !== ModTrainee::find()->where(['group_id' => $groupId])->one()) return $this->buildResponse('error', 400, 'Group does not exist');
        
        if (NULL !== ModRsTraineeGroup::find()->where(['group_id' => $groupId,'participant_id' => $participantId])->one()) return $this->buildResponse('error', 400, 'Students already exist in the group');
        
        if('修改成功' != ModRsTraineeGroup::addParticipant($groupId,[$participantId])) return $this->buildResponse('failed', 400, 'failed to add to group');
        
        return true;
    }
    
    /**
     * 设置学习小组的组长
     *
     * @param int $planId 计划id
     * @param int $groupId 小组id
     * @param string $uniqueCode 学员id
     * @return  boolean|array 成功时返回true
     * @author drce 20161129
    */
    public function setGroupLeader($planId, $groupId, $participantId)
    {
        if (empty($planId)) return $this->buildResponse('error', 400, '$planId cannot be empty');
        if (empty($groupId)) return $this->buildResponse('error', 400, '$groupId cannot be empty');
        if (empty($participantId)) return $this->buildResponse('error', 400, '$participantId cannot be empty');
        
        if (NULL === ($participant = ModParticipant::find()->where(['plan_id' => $planId, 'participant_id' => $participantId])->one())) return $this->buildResponse('error', 400, 'Students already exist in the group');
        
        if (NULL === ModRsTraineeGroup::find()->where(['group_id' => $groupId,'participant_id' => $participantId])->one()) return $this->buildResponse('error', 400, 'Students already exist in the group');
        
        $participant->setAttributes(['is_monitor' => 1,'role' => ModParticipant::TYPE_MONITOR]);
        
        if (false === $participant->save()) return $this->buildResponse('failed', 400, 'failed to participant resource');
        
        return true;
        
    }

}