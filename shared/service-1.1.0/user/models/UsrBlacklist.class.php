<?php
/**
 * Created by PhpStorm.
 * User: Win10
 * Date: 2016/12/9
 * Time: 9:46
 */

namespace service\user\models;

use service\base\db\SimpleAR;

class UsrBlacklist extends SimpleAR
{
    public static function tableName()
    { 
        return "usr_blacklist";
    }

    public static function addBlacklist($users, $reasons, $operator)
    {
        $key = ['create_time', 'operator', 'unique_code', 'reason'];
        $data = [];

        foreach ($users as $_key => $user) {
            $data[$_key][] = date('Y-m-d H:i:s');
            $data[$_key][] = $operator;
            $data[$_key][] = $user['unique_code'];
            $data[$_key][] = $reasons[$_key];
        }

        return \Yii::$app->db->createCommand()->batchInsert(static::tableName(), $key, $data)->execute();
    }

    public static function getBlacklists($where = [], $offset = 0, $limit = 0)
    {
        if (empty($offset) || empty($limit)) {
            $data = static::find($where)->orderBy('create_time desc')->asArray()->all();
        } else {
            $data = static::find()->where($where)->offset(($offset - 1) * $limit)->limit($limit)->orderBy('create_time desc')->asArray()->all();
        }
        
        return $data;
    }
}