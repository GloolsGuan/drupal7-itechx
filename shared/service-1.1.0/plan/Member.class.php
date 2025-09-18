<?php
namespace service\plan;

use service\base\Module;
use service\plan\models\Participant;
use \service\bonus\models\BonusLog as ModBonusLog;
use service\plan\interfaces\Member as MemberInterface;

class Member extends Module implements MemberInterface
{
    public $plan = null;

    public $plan_entity = null;

    public function getManagers()
    {
        return Participant::getPlanManagers($this->plan_entity->plan_id);
    }

    /**
     * 更新成员信息
     *
     * @param $participant_id
     * @param array $member_data 格式：['participant_id'=>'1', `plan_id`=>'2', `role`=>5, `name`=>'zzzz', `sort`=>10]（字段参照数据表）
     * @return \service\base\type
     */
    public function saveMember($participant_id, array $member_data = [])
    {
        $participant = Participant::getParticipant($participant_id);
        if (empty($participant))
            return $this->buildResponse('error', 400, 'participant was not found');

        $participant->setAttributes($member_data);

        if (false === $participant->save())
            return $this->buildResponse('failed', 400, 'failed to save participant');

        return $participant->getAttributes();

    }

    
    public function setManagers(array $member_data = [])
    {
        if (empty($member_data)) return false;
        //Participant::deletePlanManagers($this->plan_entity->plan_id);
        if (false === Participant::updatePlanManagers($this->plan_entity->plan_id, $member_data))
            return $this->buildResponse('failed', 400, 'failed to set managers');
        return true;
    }

      public function setManager(array $member_data = [])
    {
        if (empty($member_data)) return false;
        //Participant::deletePlanManagers($this->plan_entity->plan_id);
        if (false === Participant::updatePlanManager($this->plan_entity->plan_id, $member_data))
            return $this->buildResponse('failed', 400, 'failed to set managers');
        return true;
    }

    
    public function getMemberPlans($unique_code, array $where = [], $offset = 0, $limit = 20, $order = '')
    {
        
        return Participant::getPlans($unique_code, $where, $offset, $limit, $order);
    }

    /**
     * 设置成员状态
     *
     * @param int $participantId 成员id
     * @param int $status -1：删除；1：正常；2：禁用
     * @return 成功时返回true
     * @author drce 20161129
     */
    public function setStatus($participantId, $status)
    {
        if (empty($participantId)) return $this->buildResponse('error', 400, '$participantId cannot be empty');
        if (empty($status)) return $this->buildResponse('error', 400, '$status cannot be empty');

        $_STATUS = [-1, 1, 2];
        if (!in_array($status, $_STATUS)) return $this->buildResponse('error', 400, '$status error');

        if (($participant = Participant::find()->where(['participant_id' => $participantId])->one())) {

            $participant->setAttributes(['status' => $status]);

            if (false === $participant->save()) return $this->buildResponse('failed', 400, 'failed to add Participant resource');

            return true;
        } else {
            return $this->buildResponse('error', 400, 'participant was not found');
        }

    }

    /**
     * 修改成员的班长属性
     *
     * @param $participant_id 成员id 传值为数组类型
     * @param $status         状态，0为学员，1为班长
     * @return                成功返回true
     */
    public function setMonitor(array $participant_id, $status)
    {
        //判断传值是否为空
        if (empty($participantId)) return $this->buildResponse('error', 400, '$participantId cannot be empty');
        if (empty($status)) return $this->buildResponse('error', 400, '$status cannot be empty');

        //0为学员； 1为班长
        $_STATUS = [0, 1];

        //判断传值是否在指定范围
        if (in_array($status, $_STATUS)) {
            return $this->buildResponse('error', 400, '$status error');
        }

        foreach ($participant_id as $val) {

            if ($participant = Participant::find()->where(['participant_id' => $val])->one()) {

                $participant->setAttributes(['is_monitor' => $status]);

                if (false === $participant->save())  {
                    return $this->buildResponse('error', 400, 'failed to update date');
                }else {
                    return true;
                }
            }
        }

    }


    /**
     * @inheritDoc
     */
    public function getMemberPlansData($unique_code, array $where = [], $offset = 0, $limit = 20, $order = '')
    {
        
        if (empty($unique_code)) return $this->buildResponse('error', 400, '$unique_code cannot be empty');
        
        $query = Participant::find()->from(['participant' => Participant::tableName()])
        ->select([
            'plan.plan_id AS id',
            'plan.plan_name AS name',
            'plan.plan_type',
            'plan.plan_bt AS beginTime',
            'plan.plan_et AS endTime',
            'plan.plan_status AS status',
            'plan.credit AS credit',
            'participant.credit AS userCredit',
            'biz_payment.amount AS pay',
        ])
        ->join('LEFT JOIN', 'plan', 'plan.plan_id = participant.plan_id')
        ->join('LEFT JOIN', 'biz_payment', 'biz_payment.unique_code = participant.unique_code')
        ->where(['participant.unique_code' => $unique_code])
        ->andWhere($where);
        
        $status = empty($rows = $query->offset($offset)->limit($limit)->asArray()->all()) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $rows);
    }

    /**
     * @inheritDoc
     */
    public function getMemberTasks($unique_code, array $where = [], $offset = 0, $limit = 20, $order = '')
    {
        
        $data = [
            [
                'id' => 1,//任务id
                'name' => '任务',//任务名称
                'beginTime' => '2016-11-08 13:58:41',//任务开始时间
                'endTime' => '2016-11-08 13:58:41',//任务结束时间
                'status' => 1,//任务状态
                'doneTime' => '2016-11-10 13:58:41',//用户完成该任务的日期
            ],
            [
                'id' => 2,//任务id
                'name' => '任务',//任务名称
                'beginTime' => '2016-11-08 13:58:41',//任务开始时间
                'endTime' => '2016-11-08 13:58:41',//任务结束时间
                'status' => 1,//任务状态
                'doneTime' => '2016-11-10 13:58:41',//用户完成该任务的日期
            ]
        ];

        return $this->buildResponse('success', 201, $data);
    }

    /**
     * @inheritDoc
     */
    public function getMemberSurveys($unique_code, array $where = [], $offset = 0, $limit = 20, $order = '')
    {
        
        \Yii::$app->db_survey;
        
        if (empty($unique_code)) return $this->buildResponse('error', 400, '$unique_code cannot be empty');
        
        $query = Participant::find()->from(['participant' => Participant::tableName()])
        ->select([
            'plan.plan_id AS id',
            'plan.plan_name AS name',
            'plan.type',
            'plan.plan_bt AS beginTime',
            'plan.plan_et AS endTime',
            'plan.plan_status AS status',
            'plan.credit AS credit',
            'participant.credit AS userCredit',
            'biz_payment.amount AS pay',
        ])
        ->join('LEFT JOIN', 'plan', 'plan.plan_id = participant.plan_id')
        ->join('LEFT JOIN', 'biz_payment', 'biz_payment.unique_code = participant.unique_code')
        ->where(['participant.unique_code' => $unique_code])
        ->andWhere($where);
        
        $status = empty($rows = $query->offset($offset)->limit($limit)->asArray()->all()) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $rows);
        
        $data = [
            [
                'id' => 1,//调研id
                'name' => '调研',//调研名称
                'doneTime' => '2016-11-10 13:58:41',//用户完成该调研的日期
            ],
            [
                'id' => 2,//调研id
                'name' => '调研',//调研名称
                'doneTime' => '2016-11-10 13:58:41',//用户完成该调研的日期
            ]
        ];

        return $this->buildResponse('success', 201, $data);
    }

    /**
     * @inheritDoc
     */
    public function getMemberCredit($unique_code, array $where = [], $offset = 0, $limit = 20, $order = '')
    {
        
        if (empty($unique_code)) return $this->buildResponse('error', 400, '$unique_code cannot be empty');
        
        $query = ModBonusLog::find()->from(['bp_log' => ModBonusLog::tableName()])
        ->select([
            'bp_log.id',
            'plan.plan_name',
            'bp_log.create_time AS time',
            'bp_log.point',
            'bp_log.memo'
        ])
        ->join('LEFT JOIN', 'plan', 'plan.plan_id = bp_log.from_id AND bp_log.module=\'plan\'')
        ->where(['bp_log.unique_code' => $unique_code])
        ->andWhere($where);
        
        $status = empty($rows = $query->offset($offset)->limit($limit)->asArray()->all()) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $rows);
        
    }

    /**
     * 修改成员的权限（在数据库中以JSON格式存储）
     *
     * @param $participant_id 成员id
     * @param @auth           1：拥有权限    0：没有权限
     *                        sign                可查看签到情况
     *                        questionnaire       可查看问卷填写情况
     *                        examination         可查看试卷填写情况
     *                        information         可查看资料查看情况
     * @return                成功返回true
     */
    public function setAuth($plan_id, $participant_id, array $auth = [])
    {
        if (empty($participant_id) && empty($auth) && empty($status)) {
            return $this->buildResponse('error', 400, 'argument cannot br empty');
        }

        $auth  = json_encode($auth);
        //var_dump($auth);exit;
        $participant = Participant::find()->where(['participant_id' => $participant_id, 'plan_id' => $plan_id])->one()->setAttributes(['auth' => $auth]);

        if (false === $participant->save()) {

            return $this->buildResponse('error', 400, 'failed to update date');
        }else {

            return true;
        }
    }





}










