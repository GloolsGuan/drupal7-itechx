<?php

namespace service\activity;

use service\activity\models\Activity as ModActivity;

class Activity extends \service\base\Module
{
    public static $is_supported_private_entity = true;

    public function __construct($id, $parent = null, $config = [])
    {
        parent::__construct([]);
    }


    public function addActivity(array $data)
    {
        echo __METHOD__;
    }

    /**
     * @param array $data
     * @return array|\service\base\type
     */
    public function saveActivity(array $data)
    {
        if (empty($data)) return $this->buildResponse('error', 400, 'Invalid Parameters');
        if (isset($data['activity_id']) && !empty($data['activity_id'])) {
            if (null == ($activity = ModActivity::findOne($data['activity_id']))) return $this->buildResponse('error', 400, 'activity was not found');
        } else {
            $activity = new ModActivity();
            $activity->activity_status = 1;
        }
        $activity->setAttributes($data);
        if (false === $activity->save()) return $this->buildResponse('error', 400, 'failed to save activity');
        return $activity->getAttributes();
    }

    /**
     * @param array $activity_ids
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getActivities(array $activity_ids = [])
    {
        if (empty($activity_ids)) return [];
        $activities = ModActivity::find()->where(['activity_id' => $activity_ids])->asArray()->all();
        return $activities;
    }

    public function deleteActivities(array $activity_ids)
    {
        if (false === ModActivity::updateAll(['activity_status' => ModActivity::STATUS_DELETED], ['activity_id' => $activity_ids])) return $this->buildResponse('error', 400, 'failed to delete activity');
        return true;
    }

    public function updateActivity(array $activity_data = [])
    {
        if (!isset($activity_data['activity_id']) || empty($activity_data['activity_id'])) return $this->buildResponse('failed', 400, 'Invaliad parameters');
        if (null == ($activity = ModActivity::findOne($activity_data['activity_id']))) return $this->buildResponse('error', 400, 'activity was not found');
        $activity->setattributes($activity_data);
        if (false === $activity->save()) return $this->buildResponse('error', 400, 'failed to update activity');
        return true;
    }
}
