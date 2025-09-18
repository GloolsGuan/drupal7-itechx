<?php
/**
 *
 */

namespace service\plan\models;

use service\base\db\SimpleAR;

/**
 * Class PlanTask
 * @package service\plan\models
 * @author yangzy 20161220
 */
class PlanTask extends SimpleAR
{
    public static function tableName()
    {
        return '{{$pl_task}}';
    }

    public function rules()
    {
        return [
            ['name', 'trim'],
            ['name', 'required'],
            ['plan_id', 'exist',
                'targetClass' => 'service\plan\models\Plan',
                'targetAttribute' => 'plan_id',
                'message' => 'plan not found'],
            ['status', 'default', 'value' => static::STATUS_NORMAL],
//  'type' tinyint(11) NOT NULL COMMENT '任务类型',
//  'detail_id' int(11) DEFAULT NULL,
//  'required' tinyint(1) DEFAULT NULL,
        ];
    }
}