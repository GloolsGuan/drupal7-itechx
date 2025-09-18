<?php
namespace service\exam;

use service\exam\models\LimeSurvey;
use service\exam\models\LimeSurveyLanguageSetting;
use service\exam\models\LimeToken;
use service\exam\models\PlanExams;
use service\exam\models\DteolsSurveyExams;
use service\base\Base;

class Exam extends \service\base\Module
{
    /**
     * @param array $map
     * @param int $offset
     * @param int $limit
     * @return \service\base\type
     */

    public function getExamId()
    {
        if (false == ($exams = DteolsSurveyExams::find()->asArray()->all())) return [];
        return array_column($exams, 'survey_id');
    }

    public function getExams($where = [], $offset = 0, $limit = 10, $sort = '')
    {
        $ids = $this->getExamId();

        $map = ['in', 'surveyls_survey_id', $ids];

        $query = LimeSurveyLanguageSetting::find()->where($map)->andWhere($where);

        $totle = $query->count();
        $exams = !$sort ? $query->offset($offset)->limit($limit)->asArray()->all() : $query->offset($offset)->limit($limit)->orderBy($sort . ' desc')->asArray()->all();

        return ['exams' => $exams, 'total' => $totle];
    }

    public function getCheckExamId($plan_id = 0)
    {
        $where = ['plan_id' => $plan_id];
        if(false ==($exam = PlanExams::find()->where($where)->asArray()->all())) return [];
        return array_column($exam,'exam_id');
    }

    public function getLockedExamId($plan_id = 0)
    {
        $where = ['<>','plan_id',$plan_id];
        if(false ==($exam = PlanExams::find()->where($where)->asArray()->all())) return [];
        return array_column($exam,'exam_id');
    }

    public function savePlanExam($plan_id, $exam_id, $type)
    {
        $where = ['plan_id'=>$plan_id,'exam_id'=>$exam_id];
        if($type == 'del'){
            if(false == (PlanExams::find()->where($where))) return;
            return PlanExams::deleteAll($where);
        }elseif($type == 'add'){
            if(false != (PlanExams::find()->where($where)->one())) return;
            $planExam = new PlanExams();
            $planExam->plan_id = $plan_id;
            $planExam->exam_id = $exam_id;
            $planExam->created_at = date('Y-m-d H:i:s');
            return $planExam->save();
        }
    }

    public function saveExamParticipants($plan_id, $exam_id,$type)
    {
        $planParticipants = Base::loadService('\service\plan\Plan', [], $this)->getParticipants($plan_id);
        if($planParticipants[1] == 0) return '请先添加计划参与人员';

        $examToken = LimeToken::getSurveyId($exam_id);
        $ta = \Yii::$app->db_survey->createCommand("SHOW TABLES LIKE '" . LimeToken::tableName() . "'")->queryAll();

        if ($ta == null) {
            LimeToken::createTable($exam_id);
        }

        if($type == 'del'){
            if(false == (limeToken::find()->all())) return;
            return LimeToken::deleteAll();
        }elseif($type == 'add'){
            if(false != (limeToken::find()->all())) LimeToken::deleteAll();
            $data = [];
            foreach ($planParticipants[0] as $key => $value) {
                $data[$key]['firstname'] = $value['name'];
                $data[$key]['language'] = 'zh-Hans';
                $data[$key]['email'] = '99@dailedu.com';
                $data[$key]['lastname'] = $value['unique_code'];
            }

            return \Yii::$app->db_survey->createCommand()->batchInsert(LimeToken::tableName(), ['firstname', 'language', 'email', 'lastname'],
                $data
            )->execute();
        }


    }

    public function saveExamToken($exam_id,$type)
    {
        if($type === 'del') return;
        // 2. 设置选项，包括URL
        $url = 'http://survey.demo.dteols.cn/index.php/admin/tokens/sa/tokenify/surveyid/' . $exam_id . '/ok/Y';

        $cookie_file = dirname(__FILE__) . '/cookie.txt';

        $headers[] = 'Connection: keep-alive';
        $headers[] = 'Content-Type: application/json; encoding=utf-8';
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $compression = 'gzip';

        $process = curl_init($url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);//TODO Curl证书处理
        curl_setopt($process, CURLOPT_USERAGENT, $user_agent);

        curl_setopt($process, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($process, CURLOPT_ENCODING, $compression);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
//        if(is_file($cookie_file)) unlink($cookie_file);
        return $this->buildResponse(curl_exec($process), $process, $url);
    }


    public function delCookieFile()
    {
        $cookie_file = dirname(__FILE__) . '/cookie.txt';
        if(is_file($cookie_file)) unlink($cookie_file);//return $cookie_file;
    }

    public function getPlanExam($plan_id)
    {
        $where = ['plan_id' => $plan_id];
        if(false == ($planExam = (PlanExams::find()->where($where)->asArray()->all()))) return [];
        return $planExam;
    }

    public function getExamParticipants($survey_id = 0)
    {
        $sql = "SELECT *,'' score,'' survey_id FROM lime_tokens_".$survey_id." UNION SELECT t.*, sum(d.grade) score, d.survey_id FROM lime_tokens_".$survey_id." t LEFT JOIN dteols_exam_result d ON t.tid = d.op_token WHERE d.survey_id = ".$survey_id." GROUP BY d.op_token";

        $participants = \Yii::$app->db_survey->createCommand($sql)->queryAll();

        return $participants;
    }

    public function getAllPlanExams()
    {
        if(false ==($exam = PlanExams::find()->asArray()->all())) return [];
        return array_column($exam,'exam_id');
    }

    public function addLimeToken($exam_id,$data)
    {
        $examToken = LimeToken::getSurveyId($exam_id);
        $ta = \Yii::$app->db_survey->createCommand("SHOW TABLES LIKE '" . LimeToken::tableName() . "'")->queryAll();

        if ($ta == null) {
            LimeToken::createTable($exam_id);
        }
        $limeToken = new LimeToken();
        $limeToken->setAttributes($data);
        return $limeToken->save();
    }

    public function getExamByIds($exam_ids)
    {
        if (!is_array($exam_ids)) $ids = [$exam_ids];

        $map = ['sid' => $exam_ids];
        $items = LimeSurvey::getSurveys($map);

        if (false === $items)
            return $this->buildResponse('failed', 400, 'failed to get surveys');

        return $this->buildResponse('success', 201, $items);
    }
}