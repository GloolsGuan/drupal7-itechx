<?php
namespace service\plan\models;

use \service\base\db\SimpleAR;

/**
 * Class PlanSurveyTag
 * 计划调研的标签
 *
 * @package service\plan\models
 * @author yangzy 20161213
 */
class PlanSurveyTag extends SimpleAR
{
    public static function tableName()
    {
        return 'pl_survey_tag';
    }

    public function rules()
    {
        return [
            ['tag', 'required', 'message' => '{attribute} must not be empty'],
        ];
    }
}