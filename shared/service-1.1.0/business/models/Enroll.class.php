<?php
/**
 * 订单模型定义
 */

namespace service\business\models;

use \service\base\db\SimpleAR;
use \service\plan\models\Plan;


class Enroll extends SimpleAR
{


    public static function tableName()
    {
        return 'pl_enroll';
    }

}