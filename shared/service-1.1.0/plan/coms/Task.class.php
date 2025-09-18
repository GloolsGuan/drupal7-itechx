<?php
namespace service\plan\coms;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use service\base\Base;
use service\plan\models\RsScheduleTask;

class Task extends \service\base\Component
{
    const TYPE = 2;
    const STATUS_DELETED = -1;
    const STATUS_NORMAL = 1;

    /**
     * @var \service\task\Task
     */
    public $sTask;

    public function init()
    {
        $this->sTask = Base::loadService('\service\task\Task');
        if (false === \service\task\Task::$is_supported_private_entity) return $this->buildResponse('error', 400, 'task service does not support private entity');
    }

    public function create(array $data = [])
    {
        if (!isset($data['task']) || !is_array($data['task']) || empty($data['task'])) $data['task'] = [];
        $task_data = $data['task'];
        $schedule_id = $data['schedule_id'];
        $task_data['is_private'] = 1;
        $res = $this->sTask->saveTask($task_data);
        if ($this->isErrorResponse($res)) return $res;
        //记录关联
        RsScheduleTask::deleteAll(['schedule_id' => $schedule_id]);
        $rs = new RsScheduleTask();
        $rs->schedule_id = $schedule_id;
        $rs->task_id = $res['task_id'];
        if (false === $r = $rs->save()) return $this->buildResponse('error', 400, 'failed to save rs_schedule_task');
        $data['task'] = array_merge($task_data, $res);
        return $data;
    }

    public function remove(array $ids = [])
    {

    }

    public function getEntities(array $ids = [])
    {
        return $this->sTask->getTasks($ids);
    }

    public function update(array $data = [])
    {
        if (!isset($data['task']) || !is_array($data['task']) || empty($data['task'])) $data['task'] = [];
        $task_data = $data['task'];
        if (!isset($task_data['task_id']) || empty($task_data['task_id'])) return $this->create($data);

        $schedule_id = $data['schedule_id'];
        $task_data['is_private'] = 1;
        $res = $this->sTask->updateTask($task_data);
        if ($this->isErrorResponse($res)) return $res;

        //记录关联
        RsScheduleTask::deleteAll(['schedule_id' => $schedule_id]);
        $rs = new RsScheduleTask();
        $rs->schedule_id = $schedule_id;
        $rs->task_id = $task_data['task_id'];
        if (false === ($r = $rs->save())) return $this->buildResponse('error', 400, 'failed to save rs_schedule_task');
        return true;
    }
}