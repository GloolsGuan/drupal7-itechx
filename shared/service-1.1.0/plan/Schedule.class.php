<?php
namespace service\plan;

use service\base\Base;
use service\plan\models\RsScheduleActivity;
use service\plan\models\RsScheduleTask;
use \service\plan\models\Schedule as ModSchedule;
use service\sign\models\Sign;
use service\sign\models\SignDetail;
use yii\db\ActiveQuery;

class Schedule extends \service\base\Module
{
    protected $path_coms = '\service\plan\coms\schedule';

    public $entity = null;
    public $plan = null;

    public $coms = [];

    public function init()
    {
        $this->coms = [1 => 'Activity', 2 => 'Task'];
    }

    /**
     * @param array $schedule
     * @return bool|\service\base\type|type
     */
    public function saveSchedule(array $schedule = [])
    {
        if (!isset($schedule['schedule_id']) || empty($schedule['schedule_id'])) {
            $rs = $this->create($schedule['sche_type'], $schedule);
        } else {
            $rs = $this->updateSchedule($schedule);
        }
        return $rs;
    }

    /**
     *
     *
     *
     * @param type $plan
     * @param type $type
     * @param array $data
     * @return type
     */
    public function create($type, $data)
    {
        $model_schedule = new models\Schedule();
        //TODO
        if (!in_array($type, array_keys($this->coms))) {
            return $this->buildResponse('error', 400, 'Invalid schedule type.');
        }

        $sSign = Base::loadService('\service\sign\Sign');

        $new_sign_data = ['name' => 'plan sign'];

        $res = $sSign->create($new_sign_data);

        if ($this->isErrorResponse($res))
            return $this->buildResponse($res['status'], $res['code'], $res['data']);


        //-- Create schedule --
        $data['plan_id'] = $this->entity->plan_id;
        $data['sche_status'] = 1;
        $data['sche_type'] = $type;
        $data['sign_id'] = $res[Sign::primaryKey()[0]];
        $model_schedule->setAttributes($data);
        $schedule_result = $model_schedule->insert();
        if (false === $schedule_result) return $this->buildResponse('error', 401, $model_schedule->getFirstErrors());

        //-- Created related entity
        $plan_entity = $this->entity->getAttributes();
        $plan_entity['id'] = $this->entity->plan_id;
        $com = $this->loadCom($this->coms[$type], $plan_entity);

        $entity_data = array_merge($data, $model_schedule->getAttributes());
        $re = $com->create($entity_data);
        //\GtoolsDebug::testLog(__METHOD__, $entity_data);
        return $re;
    }

    /**
     * @param array $schedule_ids
     * @return bool|\service\base\type
     */
    public function deleteSchedules(array $schedule_ids = [])
    {
        if (empty($schedule_ids)) return $this->buildResponse('failed', 400, 'Invalid parameters');
        if (false === models\Schedule::updateAll(['sche_status' => models\Schedule::STATUS_DELETED], ['schedule_id' => $schedule_ids])) return $this->buildResponse('error', 400, 'failed to delete schedules');
        return true;
    }

    /**
     * 获取日程
     * @param array $schedule_ids 日程的id组成的数组 []
     * @param array $period 时间范围 ['from'=>'2016-01-01 0:0:0','to'=>'2016-01-05 10:10:0']
     * @return array
     */
    public function getSchedules(array $schedule_ids = [], array $period = [])
    {
        $schedule_where = [];
        $period_lt = [];
        $period_gt = [];
        $map = ['<>', 'pid', -1];
        if (!empty($schedule_ids)) $schedule_where = ['sche.schedule_id' => $schedule_ids];
        if (isset($period['from']) && !empty($period['from'])) $period_lt = ['>=', 'sche_et', $period['from']];
        if (isset($period['to']) && !empty($period['to'])) $period_gt = ['<', 'sche_bt', $period['to']];

        $aq = ModSchedule::find()->from(['sche' => ModSchedule::tableName()])
            ->with(['sign' => function (ActiveQuery $relation) {
                $relation->with(['data']);
            }])
            ->select(['sche.*', 'rs.*'])
            ->join('INNER JOIN', RsScheduleActivity::tableName() . ' AS rs', 'rs.schedule_id = sche.schedule_id')
            ->where($schedule_where)
            ->andWhere(['sche_type' => 1])
            ->andWhere(['plan_id' => $this->entity->plan_id])
            ->andWhere($period_lt)
            ->andWhere($period_gt)
            ->andWhere(['sche_status' => ModSchedule::STATUS_NORMAL])
            ->andWhere($map);
        $schedules_activity = $aq->asArray()->all();

        $activities = $this->loadCom('Activity', $this->entity->plan_id)->getEntities(array_column($schedules_activity, 'activity_id'));
        $activity_ids = array_column($activities, 'activity_id');
        foreach ($schedules_activity as &$item) {
            if (false === ($index = array_search($item['activity_id'], $activity_ids))) continue;
            $item['activity'] = $activities[$index];
        }

        $schedules_task = ModSchedule::find()->from(['sche' => ModSchedule::tableName()])
            ->select(['sche.*', 'rs.*'])
            ->join('INNER JOIN', RsScheduleTask::tableName() . ' AS rs', 'rs.schedule_id = sche.schedule_id')
            ->where($schedule_where)
            ->andWhere(['sche_type' => 2])
            ->andWhere(['plan_id' => $this->entity->plan_id])
            ->andWhere($period_lt)
            ->andWhere($period_gt)
            ->andWhere(['sche_status' => ModSchedule::STATUS_NORMAL])
            ->asArray()->all();

        $tasks = $this->loadCom('Task', $this->entity->plan_id)->getEntities(array_column($schedules_task, 'task_id'));
        $task_ids = array_column($tasks, 'task_id');
        foreach ($schedules_task as &$item) {
            if (false === ($index = array_search($item['task_id'], $task_ids))) continue;
            $item['task'] = $tasks[$index];
        }

        return array_merge($schedules_activity, $schedules_task);
    }

    public function updateSchedule(array $schedule = [])
    {
        unset($schedule['sche_type']);
        if (empty($schedule) || !isset($schedule['schedule_id']) || empty($schedule['schedule_id'])) return $this->buildResponse('failed', 400, 'Invalid parameters');
        if (null == ($schedule_ar = ModSchedule::find()->where(['schedule_id' => $schedule['schedule_id']])->andWhere(['plan_id' => $this->entity->plan_id])->one())) return $this->buildResponse('error', 400, 'schedule was not found');
        $schedule_ar->setAttributes($schedule);
        if (false === $schedule_ar->save()) return $this->buildResponse('error', 400, 'failed to save update schedule');

        $com = $this->loadCom($this->coms[$schedule_ar->sche_type], $this->entity->plan_id);
        $re = $com->update($schedule);
        if ($this->isErrorResponse($re)) return $re;
        return true;
    }


//    public function loadCom($name)
//    {
//        if (!is_string($name) || 1 > preg_match('#^[a-zA-Z]*#', $name)) {
//            return $this->buildResponse('failed', 400, 'Invalid params for ' . __MEHTOD__);
//        }
//
//        $com_ns = sprintf('%s\%s', $this->path_coms, $name);
//        $com = Yii::autoLoad(sprintf('%s\%s', $this->path_coms, $name));
//        if (class_exists($class_name)) {
//            return new $com_ns($this);
//        }
//    }

    public function getNowSchedule($plan_id, $uniqueCode, $where)
    {
        $schedule = ModSchedule::find()->from(['sche' => ModSchedule::tableName()])
            ->select(['sche.*', 'detail.*', 'sche.sign_id AS Ssign_id'])
            ->join('LEFT JOIN', Sign::tableName() . ' AS sign', 'sche.sign_id = sign.sign_id')
            ->join('LEFT JOIN', SignDetail::tableName() . ' AS detail', 'sign.sign_id = detail.sign_id')
            ->where($where)
            ->andWhere(['sche.plan_id' => $plan_id])
            ->asArray()
            ->one();var_dump($schedule);
        return $schedule;
    }

    public function addScheduleActivity($plan_id, $schedule_id)
    {
        $model_schedule_activity = new models\RsScheduleActivity();

        $data['activity_id'] = $plan_id;
        $data['schedule_id'] = $schedule_id;
        $model_schedule_activity->setAttributes($data);
        $model_schedule_activity->save();
    }

    public function addScheduleTask($plan_id, $schedule_id)
    {
        $model_schedule_task = new models\RsScheduleTask();

        $data['activity_id'] = $plan_id;
        $data['schedule_id'] = $schedule_id;
        $model_schedule_task->setAttributes($data);
        $model_schedule_task->save();
    }

    public function updaterelevancescheduleid($schedule_id, $where)
    {
        if (false === models\Schedule::updateAll(['pid' => $schedule_id], $where)) return $this->buildResponse('error', 400, 'failed to delete schedules');
        return true;
    }

    public function getScheduleById($schedule_id)
    {
        return models\Schedule::getScheduleById($schedule_id);
    }

    public function getSchedulesByPid($pid)
    {
        return models\Schedule::getSchedules(['pid' => $pid]);
    }

    public function cancelRelevance($schedule_id)
    {
        $command = models\Schedule::getDb()->createCommand('UPDATE schedule SET pid=0 WHERE pid=' . $schedule_id);
        $command->execute();
    }
}
