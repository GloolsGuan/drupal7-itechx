<?php
/**
 * 计划设置模型定义
 */

namespace service\plan\models;

use \service\base\db\SimpleAR;


class PlanSetting extends SimpleAR
{
    
    public static function tableName()
    {
        return 'pl_setting';
    }
    
}