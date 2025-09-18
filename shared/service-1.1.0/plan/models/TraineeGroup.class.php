<?php


namespace service\plan\models;

use \service\base\Error;

class TraineeGroup extends \service\base\db\ARecord
{
    static public function tableName()
    {
        return 'trainee_group';
    }

    static public function getGroups(array $where = [])
    {
        return self::find()->where($where)->asArray()->all();
    }

    static public function getGroup($group_id)
    {
        $group = static::findOne($group_id)->toArray();

        $participant = RsTraineeGroup::find()->from(['rs' => RsTraineeGroup::tableName()])
            ->select(['rs.*', 'p.*'])
            ->where(['group_id' => $group_id, 'p.status' => 1])
            ->join('LEFT JOIN', Participant::tableName() . ' AS p', 'rs.participant_id = p.participant_id')
            ->asArray()
            ->all();

        $group['participant'] = $participant;

        return $group;
    }

    static public function getPlanGroups($plan_id)
    {
        $sql = "SELECT t.*,GROUP_CONCAT(rs.participant_id) num from trainee_group t LEFT JOIN  rs_trainee_group rs
                  ON t.group_id = rs.group_id WHERE t.plan_id = " . $plan_id . " GROUP BY rs.group_id";
        $group = \Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($group as &$value) {
            if ($value['num'] == '') {
                $value['num'] = 0;
            } else {
                $value['num'] = count(explode(',', $value['num']));
            }
        }

        $participant = RsTraineeGroup::find()->from(['rs' => RsTraineeGroup::tableName()])
            ->select(['rs.*', 'p.*','t.*'])
            ->where(['t.plan_id'=>$plan_id])
            ->join('LEFT JOIN', Participant::tableName() . ' AS p', 'rs.participant_id = p.participant_id')
            ->join('LEFT JOIN', self::tableName() . ' AS t', 'rs.group_id = t.group_id')
//            ->groupBy('rs.group_id')
            ->asArray()
            ->all();

        return $data = ['group' => $group, 'participant' => $participant];
    }

    static public function getGroupById($group_id)
    {
        $where['group_id'] = $group_id;
        return self::getGroup($where);
    }

    /**
     * 获取分组的成员
     * @return array
     */
    public function getMembers($offset = null, $limit = null)
    {
        $query = $this->hasMany(Participant::className(), ['participant_id' => 'participant_id'])
            ->viaTable(RsTraineeGroup::tableName(), ['group_id' => 'group_id'])
            ->where([]);
        !is_null($offset) && $query->offset($offset);
        !is_null($limit) && $query->limit($limit);
        $participants = $query->asArray()->all();
        return $participants;
    }

    /**
     * 返回分组的成员个数
     * @return int
     */
    public function countMembers()
    {
        return $this->hasMany(Participant::className(), ['participant_id' => 'participant_id'])
            ->viaTable(RsTraineeGroup::tableName(), ['group_id' => 'group_id'])
            ->where([])->count();
    }

    static public function deleteGroup($group_id)
    {

    }

    public static function addGroup($plan_id, $group_name)
    {
        $data['plan_id'] = $plan_id;
        $data['group_name'] = $group_name;
        \Yii::$app->db->createCommand()->insert(TraineeGroup::tableName(), $data)->execute();
        return \Yii::$app->db->getLastInsertId();
    }

    public static function modGroup($group_id, $group_name)
    {
        $group = TraineeGroup::findOne($group_id);
        $group->group_name = $group_name;
        $group->save();
    }

    public
    function deleteParticipant($participant_id)
    {
        $rs = RsTraineeGroup::find()->where(['participant_id' => $participant_id, 'group_id' => $this->group_id])->one();
        if (!empty($rs)) return $rs->delete();
        if (false === $rs) return false;
        return true;
    }
}