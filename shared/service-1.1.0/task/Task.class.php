<?php


namespace service\task;

use service\base\Error;
use service\task\models\Task as ModTask;

class Task extends \service\base\Module
{
    const STATUS_DELETED = -1;
    const STATUS_NORMAL = 1;

    public static $is_supported_private_entity = true;

    public $com = [];

    public function __construct($id, $parent = null, $config = [])
    {
        parent::__construct([]);
    }

    public function init()
    {
        $this->com = [models\ExaminationTask::TYPE => 'Examination', models\SurveyTask::TYPE => 'Survey', models\DocumentTask::TYPE => 'Document'];
    }

    /**
     *添加或更新任务
     * @param array $task_data 任务的属性
     * @return bool|int|null|ModTask|static
     */
    public function saveTask(array $task_data = [])
    {
        if (empty($task_data) || !isset($task_data['task_type']) || !in_array($task_data['task_type'], array_keys($this->coms))) return $this->buildResponse('error', 400, 'Invalid task_type');
        if (isset($task_data['task_id']) && !empty($task_data['task_id'])) {
            if (null == ($task = ModTask::findOne($task_data['task_id']))) return $this->buildResponse('error', 400, 'task was not found');
        } else {
            $task = new ModTask();
            $task->task_ct = date('Y-m-d H:i:s', time());
            $task->task_status = ModTask::STATUS_NORMAL;
        }
        $task->setAttributes($task_data);
        if (false === $task->save()) return $this->buildResponse('error', 400, 'failed to save task');

        //
        $re = $this->loadCom($this->com[$task_data['task_type']])->create();
        return $re;
    }

    /**
     * @param array $task_ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getTasks(array $task_ids = [])
    {
        if (empty($task_ids)) return [];
        $tasks = ModTask::find()->where(['task_id' => $task_ids])->asArray()->all();
        return $tasks;
    }

    public function deleteTasks(array $task_ids = [])
    {
        if (false === ModTask::updateAll(['task_status' => ModTask::STATUS_DELETED], ['task_id' => $task_ids])) return $this->buildResponse('error', 400, 'failed to delete task');
        return true;
    }

    public function updateTask(array $task_data = [])
    {
        if (!isset($task_data['task_id']) || empty($task_data['task_id'])) return $this->buildResponse('failed', 400, 'Invaliad parameters to update task');
        if (null == ($task = ModTask::findOne($task_data['task_id']))) return $this->buildResponse('error', 400, 'task was not found');
        $task->setattributes($task_data);
        if (false === $task->save()) return $this->buildResponse('error', 400, 'failed to update task');
        return true;
    }
}