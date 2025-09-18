<?php


namespace service\user\models;


class Mapping extends \service\base\db\ARecord
{
    static public function tableName()
    {
        return 'mapping';
    }

    /**
     * @param $unique_code
     * @param $userid
     * @return bool
     */
    static public function create($unique_code, $userid)
    {
        $exist = static::find()->where(['unique_code' => $unique_code, 'userid' => $userid])->one();

        if (!$exist) {
            $map = new static();
            $map->userid = $userid;
            $map->unique_code = $unique_code;
            return $map->save();
        }
        return true;
    }

    /**
     * @param $unique_code
     * @return bool|false|int
     * @throws \Exception
     */
    static public function remove($unique_code)
    {
        $map = self::find()->where(['unique_code' => $unique_code])->one();
        if (empty($map)) return false;
        return $map->delete();
    }

    /**
     * @param $unique_code
     * @return bool|mixed
     */
    static public function getUserid($unique_code)
    {
        $map = self::find()->where(['unique_code' => $unique_code])->one();
        if (empty($map)) return '';
        return $map['userid'];
    }

    /**
     * @param $user_id
     * @return mixed|string
     */
    static public function getUniqueCode($user_id)
    {
        $map = self::find()->where(['userid' => $user_id])->one();
        if (empty($map)) return '';
        return $map['unique_code'];
    }
}