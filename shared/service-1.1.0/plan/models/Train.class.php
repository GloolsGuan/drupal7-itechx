<?php
namespace service\plan\models;

use service\base\db\SimpleAR;

/**
 * Class Plan
 *
 * @package service\plan\models
 */
class Train extends SimpleAR
{
    /**
     * 状态：-1：删除；1：未发布，2：已发布
     */
    const STATUS_DELETED = -1;
    const STATUS_NORMAL = 1;
    const STATUS_PUBLISHED = 2;


    public static function tableName()
    {
        return 'plan';
    }

    public static function primaryKey()
    {
        return ['plan_id'];
    }

    public function rules()
    {
        return [
            ['update_time', 'default', 'value' => date('Y-m-d H:i:s', time())],
        ];
    }

}













