<?php


namespace service\plan\models;


class RsPlanResource extends \com\yii\db\ARecord
{
    static public function tableName()
    {
        return 'rs_plan_resource';
    }

    /**
     * @param $where
     * @param int $offset
     * @param int $limit
     */
    static public function getPlanResourceIds($plan_id)
    {
        $res = self::find()->where(['plan_id' => $plan_id])->asArray()->all();
        return array_column($res, 'resource_id');
    }

    static public function countResources($plan_id)
    {
        return self::find()->where(['plan_id' => $plan_id])->count();
    }


    /**@author fenh
     *获取指定的resource
     * */
    static public function getPlanResourceUsersIds($resource_id)
    {
        $res = self::find()->where(['resource_id' => $resource_id])->asArray()->all();
        return array_column($res, 'resource_id');
    }

}