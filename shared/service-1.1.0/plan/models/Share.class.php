<?php
/**
 *
 */

namespace service\plan\models;

use service\base\db\SimpleAR;

class Share extends SimpleAR
{
    public static function tableName()
    {
        return '{{%pl_shares}}';
    }

    public function rules()
    {
        return [
            [['title'], 'trim'],
            [['title', 'unique_code'], 'required'],
            [['unique_code'], 'string', 'length' => [64, 64]],
            ['plan_id', 'exist',
                'targetClass' => 'service\plan\models\Plan',
                'targetAttribute' => 'plan_id',
                'message' => 'plan not found.'],
            ['file_id', 'exist',
                'targetClass' => 'service\file\models\File',
                'targetAttribute' => 'file_id',
                'message' => 'file was not found.'],
            [['download_counts'], 'default', 0],
            [['status'], 'default', static::STATUS_NORMAL],
        ];
    }
}