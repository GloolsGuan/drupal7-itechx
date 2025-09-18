<?php
namespace service\plan\coms;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use service\plan\models\RsScheduleActivity;

class Activity extends \service\base\Component
{
    const TYPE = 1;
    const STATUS_DELETED = -1;
    const STATUS_NORMAL = 1;

    /**
     * @var \service\activity\Activity
     */
    public $sActivity;

    public function init()
    {
        $this->sActivity = \service\base\Base::loadService('\service\activity\Activity');
        if (false === \service\activity\Activity::$is_supported_private_entity) return $this->buildResponse('error', 400, 'activity service does not support private entity');
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data = [])
    {
        $schedule_id = $data['schedule_id'];
        $data['is_private'] = 1;
        $res = $this->sActivity->saveActivity($data);
        if ($this->isErrorResponse($res)) return $res;
        //记录关联
        RsScheduleActivity::deleteAll(['schedule_id' => $schedule_id]);
        $rs = new RsScheduleActivity();
        $rs->schedule_id = $schedule_id;
        $rs->activity_id = $res['activity_id'];
        if (false === $r = $rs->save()) return $this->buildResponse('error', 400, 'failed to save rs_schedule_activity');
        
        //\GtoolsDebug::testLog(__METHOD__, [$r, $data, $res]);
        return array_merge($data, $res);
    }

    /**
     * @param array $ids
     * @return bool|\service\base\type
     */
    public function remove(array $ids = [])
    {
        return $this->sActivity->deleteActivities($ids);
    }

    /**
     * @param array $ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getEntities(array $ids = [])
    {
        return $this->sActivity->getActivities($ids);
    }

    /**
     * @param array $data
     * @return bool|mixed|\service\base\type
     */
    public function update(array $data = [])
    {
        if (!isset($data['activity_id']) || empty($data['activity_id'])) return $this->create($data);

        $schedule_id = $data['schedule_id'];
        $data['is_private'] = 1;
        $res = $this->sActivity->updateActivity($data);
        if ($this->isErrorResponse($res)) return $res;

        //记录关联
        RsScheduleActivity::deleteAll(['schedule_id' => $schedule_id]);
        $rs = new RsScheduleActivity();
        $rs->schedule_id = $schedule_id;
        $rs->activity_id = $data['activity_id'];
        if (false === ($r = $rs->save())) return $this->buildResponse('error', 400, 'failed to save rs_schedule_activity');
        return true;
    }
}