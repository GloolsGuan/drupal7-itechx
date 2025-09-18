<?php

namespace service\plan\models;

    /**
     * CREATE TABLE `participant` (
     * `participant_id` int(11) NOT NULL AUTO_INCREMENT,
     * `plan_id` int(11) DEFAULT NULL,
     * `role` int(11) DEFAULT NULL COMMENT '1：主持人、2：讲师、3：学员',
     * `status` int(11) DEFAULT NULL COMMENT '-1：删除；1：正常',
     * `confirmed` int(255) DEFAULT NULL,
     * `unique_code` char(64) DEFAULT NULL,
     * `name` varchar(255) DEFAULT NULL,
     * `is_monitor` int(255) DEFAULT NULL,
     * `auth` text,
     * PRIMARY KEY (`participant_id`)
     * ) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
     */


/**
 * Class Participant
 * @package service\plan\models
 *
 */
class Participant extends \service\base\db\ARecord
{
    /**
     * 状态：-1：删除；1：正常；2：禁用
     */
    const STATUS_DELETED = -1;
    const STATUS_NORMAL = 1;
    const STATUS_DISABLE = 2;

    /**
     * PlanEmcee：主持人、PlanTeacher：讲师、PlanStudent：学员、PlanMonitor：班长、PlanManager：管理员
     */
    const TYPE_EMCEE = 'PlanEmcee';
    const TYPE_LECTUER = 'PlanTeacher';
    const TYPE_TRAINEE = 'PlanStudent';
    const TYPE_MONITOR = 'PlanMonitor';
    const TYPE_MANAGER = 'PlanManager';
    const TYPE_PARTICIPANT = 'participant';

    const UNCONFIRMED = 0;
    const CONFIRMED = 1;


    public static function tableName()
    {
        return 'participant';
    }

    public function rules()
    {
        return [
            [['plan_id', 'unique_code'], 'required'],
            [['role'], 'in', 'range' => [static::TYPE_EMCEE, static::TYPE_PARTICIPANT, static::TYPE_LECTUER, static::TYPE_TRAINEE, static::TYPE_MONITOR, static::TYPE_MANAGER], 'message' => 'invalid role']
        ];
    }

    public static function primaryKey()
    {
        return ['participant_id'];
    }

    public function isTrainee()
    {
        return $this->role == static::TYPE_TRAINEE;
    }

    /**
     * 获取计划的参与人员
     * @param $plan_id
     * @param array $map
     * @param int $offset
     * @param null $limit 为null时表示查询全部
     * @return array
     */
    public static function getParticipants($plan_id, array $map = [], $offset = 0, $limit = null)
    {
        $map['plan_id'] = $plan_id;
        $count = static::find()->where($map)->count();
        $query = static::find()->where($map)->offset($offset);
        !is_null($limit) && $query->limit($limit);
        $list = $query->asArray()->orderBy('sort desc')->all();
        return [$list, $count];
    }

    /**
     * 获取参与人员详情
     * @param $participant_id
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function getParticipant($participant_id)
    {
        return static::getMember(['participant_id' => $participant_id]);
    }

    public static function getMember(array $where = [])
    {
        return static::find()->where($where)->one();
    }

    /**
     * 获取计划的参与人员（没有分页）
     * @param array $map
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getMembers(array $map = [])
    {
        return static::find()->where($map)->asArray()->all();
    }

    public static function getMonitors(array $where = [])
    {
        $where['is_monitor'] = 1;
        $where['role'] = self::TYPE_MONITOR;

        array_unshift($where, 'or');

        return static::getMembers($where);
    }

    public static function setMonitors(array $where = [])
    {

        return static::updateAll(['is_monitor' => 1, 'role' => static::TYPE_MONITOR], $where);
    }

    public static function removeMonitors(array $where = [])

    {
        //$where['is_monitor'] = 1;
        return static::updateAll(['is_monitor' => 0, 'role' => static::TYPE_TRAINEE], $where);
    }

    /**
     * @param $plan_id
     * @param array $monitors [
     * [ unique_code: 'cab1c9dc2d3f928518b654554c2320fbf9fa07d0d0a677e26204dfee6165ff7c',
     * auth: [ view_sign: 1, view_survey: 0, view_examination: 1, view_download_file: 0 ] ]
     * ]
     */
    public static function setMonitorsAuth($plan_id, array $monitors = [])
    {
//        foreach ($monitors as &$monitor) {
//            $monitor['plan_id'] = $plan_id;
//            $monitor['auth'] = json_encode($monitor['auth']);
//        }
    }

    public function isMonitor()
    {
        return $this->is_monitor && $this->role == static::TYPE_TRAINEE;
    }

    /**
     * @param array $auth [ view_sign: 1, view_survey: 0, view_examination: 1, view_download_file: 0 ]
     */
    public function setMonitorAuth(array $auth = [])
    {
        $this->auth = json_encode($auth);
        return $this;
    }

    /**
     * @param $plan_id
     * @param array $where
     * @return array
     */
    public static function getTrainees($plan_id, array $where = [])
    {
        $where['plan_id'] = $plan_id;
        $where['role'] = static::TYPE_TRAINEE;
        return static::getMember($where);
    }

    /**
     * 确认参与计划
     */
    public function confirm()
    {
        $this->confirmed = static::CONFIRMED;
        return $this->save();
    }

    /**
     * 获取计划的管理
     * @param $plan_id
     */
    public static function getPlanManagers($plan_id)
    {
        $where['role'] = static::TYPE_MANAGER;
        return static::getMembers($where);
    }

    /**
     * 删除计划的管理员
     * @param $plan_id
     * @return int
     */
    public static function deletePlanManagers($plan_id)
    {
        $where['role'] = static::TYPE_MANAGER;
        return static::deleteAll($where);
    }

    /**
     * 为计划添加管理员
     * @param $plan_id
     * @param array $member_data [['unique_code'=>'aaa'],['uniique_code'=>'bbb']]
     * @return bool|int
     * @throws \yii\db\Exception
     */
    public static function addPlanManagers($plan_id, array $member_data = [])
    {
        if (empty($member_data)) return false;

        $data = [];
        foreach ($member_data as &$item) {
            $item['plan_id'] = $plan_id;
            $item['role'] = static::TYPE_MANAGER;
            $data[] = array_values($item);
        }

        return static::find()->createCommand()->batchInsert(static::tableName(), array_keys($item), $data)->execute();

    }
   /**
    * 更新计划角色
    */
    public function updatePlanManagers($plan_id,array $member_data = []){
        if (empty($member_data)) return false;
        $where = ['AND',['=','plan_id',$plan_id],['=','role',Participant::TYPE_MANAGER]];
        Participant::updateAll(['role' =>Participant::TYPE_PARTICIPANT], $where);
        foreach ($member_data as $item) {
            $map =['AND',['=','plan_id',$plan_id],['=','unique_code',$item['unique_code']]];
           $participant = Participant::getMember($map);
           $item['role'] = Participant::TYPE_MANAGER;
           $participant->setAttributes($item);
           $participant->save();
        }
    }

public function updatePlanManager($plan_id,array $member_data = []){
        foreach ($member_data as $item) {
            $map =['AND',['=','plan_id',$plan_id],['=','unique_code',$item['unique_code']]];
           $participant = Participant::getMember($map);
           $item['role'] = Participant::TYPE_MANAGER;
           $participant->setAttributes($item);
           $participant->save();
        }
    }
    
    public static function getPlans($unique_code, array $where = [], $offset = 0, $limit = 20, $order = '')
    {
        $plans = Plan::getMemberPlans($unique_code, $where, $offset, $limit, $order);
        return $plans;
    }


}