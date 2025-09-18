<?php
/**
 * Created by PhpStorm.
 * User: Win10
 * Date: 2016/9/29
 * Time: 9:45
 */

namespace service\plan\models;

use  \service\base\db\SimpleAR;

class PlanDiscussionParticipant extends SimpleAR
{
    public static function tableName()
    {
        return 'plan_discussion_participant';
    }

    public static function addPlanDiscussionParticipant($plan_id, $discussion_id, $participant)
    {
         foreach ($participant as &$item) {
                $item['discussion_id'] = $discussion_id;
                $item['plan_id'] = $plan_id;
            }
            \Yii::$app->db->createCommand()->batchInsert(self::tableName(),
                [ 'user_name', 'unique_code', 'discussion_id', 'plan_id'],
                $participant)
                ->execute();
    }

    public static function delPlanDiscussionParticipant($plan_id, $discussion_id, $participant)
    {
        $where['plan_id'] = $plan_id;
        $where['discussion_id'] = $discussion_id;

        return static::deleteAll($where);
    }
}