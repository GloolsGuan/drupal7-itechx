<?php
namespace service\plan\models;

use service\base\db\SimpleAR;

/**
 * Class PlanTag
 * @package service\plan\models
 */
class PlanTag extends SimpleAR
{
    static public function tableName()
    {
        return 'plan_tag';
    }
}