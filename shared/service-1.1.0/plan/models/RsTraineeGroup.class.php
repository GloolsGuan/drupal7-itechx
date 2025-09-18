<?php


namespace service\plan\models;


class RsTraineeGroup extends \service\base\db\SimpleAR
{
    static public function tableName()
    {
        return 'rs_trainee_group';
    }

    public static function addParticipant($group_id, $participant_id)
    {
        $participant = [];
        foreach ($participant_id as $key => $value) {
            $participant[$key][] = $group_id;
            $participant[$key][] = $value;
        }
        \Yii::$app->db->createCommand()->batchInsert(RsTraineeGroup::tableName(),
            ['group_id', 'participant_id'],
            $participant)
            ->execute();
        return '修改成功';
    }
}