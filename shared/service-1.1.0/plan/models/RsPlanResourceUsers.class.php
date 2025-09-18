<?php
/**
 * Created by PhpStorm.
 * User: xiaohu
 * Date: 2017/4/10
 * Time: 11:18
 */

namespace service\plan\models;


class RsPlanResourceUsers extends \com\yii\db\ARecord
{
    static public function tableName()
    {
        return 'rs_plan_resource_users';
    }

    /**
     * @param $where
     * @param int $offset
     * @param int $limit
     */
    static public function getPlanResourceIds($resource_id)
    {
        $res = self::find()->where(['resource_id' => $resource_id])->asArray()->all();
        return array_column($res, 'resource_id');
    }

    static public function countResources($resource_id)
    {
        return self::find()->where(['resource_id' => $resource_id])->count();
    }
    /**@author fenh
     *获取指定的resource
     * */
    /*static public function getPlanResourceUsersIds($user_id)
    {
        $res = self::find()->where(['user_id' => $user_id])->asArray()->all();
        return array_column($res, 'user_id');
    }*/

}