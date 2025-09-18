<?php


namespace service\plan\models;

use service\base\db\SimpleAR;


class Enroll extends SimpleAR
{

    public static function tableName()
    {
        return 'pl_enroll';
    }

    public static function primaryKey()
    {
        return ['id'];
    }

    public function rules()
    {
        return [
            [['plan_id', 'begin_time', 'end_time'], 'required'],
            ['plan_id', 'exist',
                'targetClass' => 'service\plan\models\Plan',
                'targetAttribute' => 'plan_id',
                'message' => 'plan not found.'],
            [['begin_time', 'end_time'], 'date', 'format' => 'yyyy-MM-dd HH:mm:ss'],
            ['end_time', 'compare', 'compareAttribute' => 'begin_time', 'operator' => '>'],
            [['limited'], 'number', 'min' => 0],
            ['cash_price', 'double', 'min' => 0],
        ];
    }

    public static function getItemByPlanId($plan_id)
    {
        $where = [];
        $where['plan_id'] = $plan_id;
        return static::find()->where($where)->one();
    }

    /*
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => static::STATUS_NORMAL]
        ];
    }
    */
}