<?php


namespace service\plan\models;

use  \service\base\db\SimpleAR;

class Discussion extends SimpleAR
{
    public static function tableName()
    {
        return 'plan_discussion';
    }

    public function rules()
    {
        return [
            [['name'], 'required']
        ];
    }

    public static function getDiscussions(array $map = [], $offset = 0, $limit = 20)
    {
        $query = static::find()->where($map);
//        $where['p.plan_id'] = $map['plan_id'];
//        $query = static::find()->from(['d' => static::tableName()])
//            ->select(['d.*', 'p.*'])
//            ->where($where)
//            ->join('LEFT JOIN', PlanDiscussionParticipant::tableName() . ' AS p', 'd.id = p.discussion_id');

        $discussions = $query->offset($offset)->limit($limit)->orderBy('create_time desc')->asArray()->all();
        return [$discussions, $query->count()];
    }


    public static function addPlanDiscussion($plan_id, array $data)
    {
        $data['plan_id'] = $plan_id;
        \Yii::$app->db->createCommand()->insert(self::tableName(), $data)->execute();
        return \Yii::$app->db->getLastInsertId();
    }

    public static function getDiscussionInfo($discussion_id, $offset, $limit)
    {
        $where['discussion_id'] = $discussion_id;
        $discussion = static::findOne($discussion_id)->toArray();
//        $participants = PlanDiscussionParticipant::find()->where($where)->asArray()->all();
        $participants = PlanDiscussionParticipant::find()->where($where)->offset($offset)->limit($limit)->asArray()->all();
        $totalCount = PlanDiscussionParticipant::find()->where($where)->asArray()->all();
        $discussion['participants'] = $participants;
        $discussion['totalCount'] = count($totalCount);
        return $discussion;
        return ModDiscussion::getDiscussions($map, $offset, $limit);
    }
}
