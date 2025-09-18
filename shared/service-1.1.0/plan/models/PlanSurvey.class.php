<?php
namespace service\plan\models;

use \service\base\db\SimpleAR;

class PlanSurvey extends SimpleAR
{
    public static function tableName()
    {
        return 'plan_surveys';
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => static::STATUS_NORMAL],
            ['created_at', 'default', 'value' => date('Y-m-d H:i:s', time())],
        ];
    }

    public static function savePlanSurvey($plan_id, $survey_id)
    {
        $where['plan_id'] = $plan_id;
        $where['survey_id'] = $survey_id;

        if (false != ($planSurvey = static::find()->where($where)->one())) {

            static::deleteAll($where);
        } else {

            $planSurvey = new static();
            $data['plan_id'] = $plan_id;
            $data['survey_id'] = $survey_id;
            $data['status'] = 1;
            $planSurvey->setAttributes($data);
            return $planSurvey->save();
        }


    }

    public static function getSurveyLocked($plan_id)
    {
        $where[] = 'AND';
        $where[] = ['<>', 'plan_id', $plan_id];
//        if (false == static::find()->where(['plan_id'=>$plan_id])->all()) return;

        if (false == ($surveyLocked = (static::find()->where($where)->asArray()->all()))) return [];
        return $surveyLocked;
    }
}