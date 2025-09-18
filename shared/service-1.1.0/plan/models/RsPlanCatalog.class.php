<?php
/**
 * 课程目录与计划的关联模型定义
 */

namespace service\plan\models;

use service\base\db\SimpleAR;

class RsPlanCatalog extends SimpleAR
{
    public static function tableName()
    {
        return 'pl_rs_plan_catalog';
    }
}