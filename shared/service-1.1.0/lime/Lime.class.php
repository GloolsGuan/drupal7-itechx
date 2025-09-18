<?php
namespace service\survey;

use service\survey\models\LimeSurvey;
use service\survey\models\LimeSurveyLanguageSetting;
use service\survey\models\limeToken;

class Lime extends \service\base\Module
{
    /**
     * @param array $map
     * @param int $offset
     * @param int $limit
     * @return \service\base\type
     */

    public $type;

    /**
     * 获取类型 [调研，考试]
     * @param string $type
     */
    public function getType($type)
    {
        $this->type = $type;
    }

    /**
     * 获取计划关联的调研考试
     * @param $sid
     * @return bool
     */
    public function getPlanLime($plan_id)
    {
        if ('survey' == $this->type) {
            return $this->getSurveys($plan_id);
        } elseif ('exam' == $this->type) {
            return $this->getExams($plan_id);
        } else {
            return false;
        }
    }



    /**
     * 获取考试列表
     * @param int $plan_id
     * @param string $resource
     * @return array
     */
    public function getExams($plan_id = 0, $resource = '')
    {
        return [];
    }

    /**
     * 获取调研列表
     * @param $plan_id
     * @param string $resource
     * @return array
     */
    public function getSurveys($plan_id, $resource = '')
    {
        return [];
    }

    /**
     * 根据id获取单条lime信息
     * @param int $sid
     * @return array
     */
    public function getLimeById($sid = 0)
    {
        return [];
    }

    /**
     * 根据id数组获取lime信息
     * @param array $sids
     * @return array
     */
    public function getLimesByIds($sids = [])
    {
        return [];
    }




//    public function getSurveys(array $map = [], $offset = 0, $limit = 20)
//    {
//        $surveys = LimeSurvey::getSurveys($map, $offset, $limit);
//
//        if (false === $surveys)
//            return $this->buildResponse('error', 400, 'failed to get surveys');
//
//        return $surveys;
//    }

    /**
     * @param $ids
     * @return array
     */
    public function getSurveysById($ids)
    {
        if (!is_array($ids))
            $ids = [$ids];

        if (empty($ids))
            return $this->buildResponse('error', 400, 'id was empty');

        $surveys = LimeSurveyLanguageSetting::getItems([LimeSurveyLanguageSetting::primaryKey()[0] => $ids]);

        if (false === $surveys)
            return $this->buildResponse('failed', 400, 'failed to get surveys');

        return $surveys;
    }

    public function getSurveyById($id)
    {
        $survery = LimeSurveyLanguageSetting::getItemById($id);

        if (false === $survery)
            return $this->buildResponse('failed', 400, 'failed to get survey');

        return $survery->getAttributes();
    }

    public function getSurveyToken($survey_id = 0, $login_name = '')
    {
        $limeToken = limeToken::getSurveyId($survey_id);
        return limeToken::getSurveyToken($login_name);
    }

    public function addMemers($members)
    {
        return LimeToken::addSurveyParticipant($members);
    }

    public function getSurveyDetail($id, $unique_code)
    {
        return LimeSurveyLanguageSetting::getSurveyDetail($id, $unique_code);

    }

    public function getLimeToken($survey_id)
    {
        $survey = limeToken::getSurveyId($survey_id);
        return limeToken::find()->asArray()->all();
    }
}