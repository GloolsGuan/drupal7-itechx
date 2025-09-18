<?php
/**
 *
 */

namespace service\plan\models;

use service\base\db\SimpleAR;
use yii\db\Expression;

/**
 * Class PlanTaskLog
 * @package service\plan\models
 * @author yangzy 20161220
 */
class PlanTaskLog extends SimpleAR
{
    public static function tableName()
    {
        return '{{%pl_task_log}}';
    }

    public function rules()
    {
        return [
            ['task_id', 'exist',
                'targetClass' => 'service\plan\models\PlanTask',
                'targetAttribute' => 'id',
                'message' => 'PlanTask not found'],
            [['unique_code'], 'string', 'length' => [64, 64]],
            ['create_time', 'default', 'value' => new Expression('NOW()')],
        ];
    }
}