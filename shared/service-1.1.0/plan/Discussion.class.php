<?php


namespace service\plan;

use \service\base\Module;
use \service\plan\models\Discussion as ModDiscussion;
use \service\plan\models\Participant;
use \service\plan\models\PlanDiscussionParticipant;
use \service\plan\models\Plan as ModPlan;

class Discussion extends Module
{

    public function create(array $data = [])
    {
        $data['unique_name'] = sha1($data['name']);

        if (false === ($discussion = ModDiscussion::createItem($data))) return $this->buildResponse('failed', 400, 'failed save discussion');
        return $discussion->getAttributes();
    }

    public function close(array $ids = [])
    {
        return ModDiscussion::deleteItemById($ids);
    }

    public function getDiscussions($map = [], $offset = 0, $limit = 20)
    {
        return ModDiscussion::getDiscussions($map, $offset, $limit);
    }

    /**
     * editDiscussionTitle
     *
     */
    public function editTitle($plan_id, $id, $name)
    {

        $discussion = ModDiscussion::findOne($id);//->where('id='.$id)->one();
        $discussion->name = $name;

        return $discussion->save();
    }

    /**
     * @param $plan_id
     * @return array
     * 获取计划参与人员
     */
    public function getPlanParticipant($plan_id)
    {
        $map[] = 'AND';
        $map[] = ['=', 'plan_id', $plan_id];
        $map[] = ['in', 'role', ['PlanStudent', 'PlanMonitor']];

        return Participant::find()->where($map)->asArray()->all();

    }

    /**
     * @param $plan_id
     * @param $discussion_id
     * @return array
     * 获取讨论参与人员
     */
    public function getDiscussionParticipant($plan_id, $discussion_id)
    {
        return PlanDiscussionParticipant::find()->where(['plan_id' => $plan_id, 'discussion_id' => $discussion_id])->asArray()->all();
    }

    /**
     * @param $plan_id
     * @param $data
     * @param array $participant
     */
    public function saveDiscussion($plan_id, $discussion_id, $data, array $participant)
    {

        if (!ModPlan::findOne($plan_id)) return false;
        if (empty($discussion_id)) {
            $discussion_id = \service\plan\models\Discussion::addPlanDiscussion($plan_id, $data);
        } else {
            PlanDiscussionParticipant::delPlanDiscussionParticipant($plan_id, $discussion_id, $participant);
        }
        if (!empty($participant))
            PlanDiscussionParticipant::addPlanDiscussionParticipant($plan_id, $discussion_id, $participant);

        return \service\plan\models\Discussion::getItemById($discussion_id)->getAttributes();
    }

    /**
     * @param $plan_id
     * @param $discussion_id
     * @return array
     * 获取讨论组详细信息
     */
    public function getDiscussionInfo($plan_id, $discussion_id, $offset = 0, $limit = 16)
    {
        if (!ModPlan::findOne($plan_id)) return false;
        return ModDiscussion::getDiscussionInfo($discussion_id, $offset, $limit);
    }

    /**
     * 获取讨论组详情
     *
     * @param $id
     * @return \service\base\type
     * @author yzy
     * @date 20161110
     */
    public function getDisscussion($id)
    {
        if (false === $discussion = ModDiscussion::getItemById($id))
            return $this->buildResponse('failed', 400, 'discussion was not found');

        return $this->buildResponse('success', 201, $discussion->getAttributes());
    }

}