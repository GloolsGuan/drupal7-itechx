<?php


namespace service\plan\models;

use service\base\db\SimpleAR;

/**
 * Class RsPlanSurveyTag
 * @package service\plan\models
 * @author yangzy 20161213
 */
class RsPlanSurveyTag extends SimpleAR
{
    static public function tableName()
    {
        return 'pl_rs_survey_tag';
    }
}