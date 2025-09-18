<?php
/**
 *
 */

namespace service\log\models;

use service\base\db\SimpleAR;
use yii\db\Expression;

/**
 * Class Log
 * @package service\log\models
 * @author yangzy 20161214
 */
class Log extends SimpleAR
{
    public static function tableName()
    {
        return 'lg_log';
    }

    public function rules()
    {
        return [
            ['unique_code', 'string', 'length' => [64, 64]],
            ['action_id', 'exist',
                'targetClass' => 'service\log\models\Action',
                'targetAttribute' => 'id',
                'message' => 'action not exist'],
            ['create_time', 'default', 'value' => new Expression('NOW()')],
        ];
    }
}