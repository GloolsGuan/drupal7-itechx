<?php
namespace service\survey;

use service\base\Module;
use service\survey\interfaces\LimeSurvey as LimeSurveyInterface;
use service\survey\models\LimeSurvey;
use service\survey\models\LimeSurveyLanguageSetting;
use service\survey\models\limeToken;
use service\survey\models\LimeSurveyAnswers;
use service\survey\models\LimeQuestions;
use service\survey\models\LimeAnswers;
use service\survey\org\jsonrpcphp\JsonRPCClient as JsonRPCClient;
use yii\db\ActiveQuery;

use service\plan\models\Participant;
use yii\helpers\ArrayHelper;

class Lime extends Module implements LimeSurveyInterface
{
    public function getSurveys(array $map = [], $offset = -1, $limit = -1)
    {
        $items = LimeSurvey::getSurveys($map, $offset, $limit);
        if (false === $items)
            return $this->buildResponse('error', 400, 'failed to get surveys');
        return $this->buildResponse('success', 201, $items);
    }

    public function getSurveysById(array $ids)
    {
        if (!is_array($ids)) $ids = [$ids];

        $map = [LimeSurvey::primaryKey()[0] => $ids];
        $items = LimeSurvey::getSurveys($map);

        if (false === $items)
            return $this->buildResponse('failed', 400, 'failed to get surveys');

        return $this->buildResponse('success', 201, $items);
    }

    public function getSurveyById($id)
    {
        $map = [LimeSurvey::primaryKey()[0] => $id];
        $items = LimeSurvey::getSurveys($map);

        if (false === $items)
            return $this->buildResponse('failed', 400, 'failed to get survey');
        if (empty($items)) {
            $item = [];
        } else {
            $item = $items[0];
        }
        return $this->buildResponse('success', 201, $item);
    }

    public function getSurveyToken($survey_id = 0, $unique_code = '')
    {
        $limeToken = limeToken::getSurveyId($survey_id);
        return limeToken::getSurveyToken($unique_code);
    }

    public function addMemers($members)
    {
        return LimeToken::addSurveyParticipant($members);
    }

    public function getSurveyDetail($id, $unique_code)
    {
        return LimeSurveyLanguageSetting::getSurveyDetail($id, $unique_code);

    }

    public function getLimeToken($survey_id, array $where = [])
    {
        $survey = limeToken::getSurveyId($survey_id);
        return limeToken::find()->where($where)->asArray()->all();
    }


    public function getLimeSurvey($survey_id, array $where = [])
    {
        $survey = LimeSurveyAnswers::getSurveyId($survey_id);
        return LimeSurveyAnswers::find()->where($where)->asArray()->all();
    }

    /**
     * 获取调研结果 调研的问题及子问题
     * @param array $where
     * @return \service\base\type
     * @author qinjy 20170401
     */
    public function getSurveyResult($sid)
    {
        $cont = [];
        $map = ['active' => 'Y'];
        $surveyResult = LimeSurvey::find()->select(['sid', 'active'])
            ->with(['surveyContent' => function (ActiveQuery $relation) use ($cont, $sid) {
                $relation->select(['surveyls_survey_id', 'surveyls_title', 'surveyls_description', 'surveyls_welcometext', 'surveyls_endtext'])
                    ->where($cont);
            }])
            ->with(['questions' => function (ActiveQuery $relation) use ($sid) {
                $relation->where(['sid' => $sid, 'parent_qid' => 0])->orderBy(['qid' => SORT_ASC])
                    ->with(['childQuestions' => function (ActiveQuery $relation) use ($sid) {
                        $relation->where(['sid' => $sid])->orderBy(['qid' => SORT_ASC]);
                    }]);
            }])
            ->where($map)
            ->andWhere(['sid' => $sid])
            ->asArray()
            ->all();

        if (false === $surveyResult)
            return $this->buildResponse('error', 400, 'failed to get survey result');

        empty($surveyResult) ? $code = 200 : $code = 201;

        return $this->buildResponse('success', $code, $surveyResult);
    }


    /**
     * admin:管理员，presenter:主持人，participant:参与者，follower:关注者
     * 根据角色获取数据
     */
    public function getRole(array $where)
    {
        $map[] = 'AND';
        $map[] = ['status' => Participant::STATUS_NORMAL];
        $map[] = $where;
        return Participant::getMember($map);
    }


    /**
     * 获取计划下用户及用户参与调研情况
     * @param $plan_id
     * @param $survey_id
     * @return array
     * @author qinjy 20170401
     */
    public function getSurveyUsers($plan_id, $survey_id)
    {
        $user = \Yii::loadService('user');
        $plan = \Yii::loadService('plan');
        $participants = $plan->getParticipants($plan_id);

        if (isset($participants[0]) && !empty($participants[0])) {
            $user_unique_codes = array_unique(ArrayHelper::getColumn($participants[0], 'unique_code'));
        } else {
            return $participants;
        }

        $userData = $user->loadListByCodes($user_unique_codes);
        $surveyTokensUser = $this->getLimeToken($survey_id);

        $userInfo = [];
        if (('success' == $userData['status']) && !empty($userData['data'])) {
            $userInfo = ArrayHelper::index($userData['data'], 'unique_code');
        }
        $userToken = [];
        if (!empty($surveyTokensUser)) {
            $userToken = ArrayHelper::index($surveyTokensUser, 'lastname');
        }

        //合并数据
        foreach ($userInfo as $key => &$val) {
            if (isset($userToken[$key])) {
                $val['survey_token'] = $userToken[$key];
            } else {
                $val['survey_token'] = [];
            }
        }

        return $userInfo;
    }


    /**
     * 调研详细资料
     * @param $survey_id
     * @return array|null
     */
    public function getSurveyLanguageSetting($survey_id)
    {
        $survey = LimeSurveyLanguageSetting::findOne($survey_id);
        if (!empty($survey)) {
            $result = ArrayHelper::toArray($survey);
        } else {
            return null;
        }
        return $result;
    }


    /**
     * 根据sid获取调研详情 BY 吴瑾
     * @param $sid
     * @return array
     */
    function getSurveysDetailBySid($sid)
    {
        if (!$sid) {
            return $this->buildResponse('error', 400, 'failed to get sid');
        };
        $result = [];
        $LimeSurvey = LimeSurvey::find()->where(["sid" => $sid])->asArray()->one();
        if ($LimeSurvey['active'] == "N") {
            return $this->buildResponse('error', 400, '该调研已关闭');
        };
        $LimeSurveyLanguageSetting = LimeSurveyLanguageSetting::find()->where(["surveyls_survey_id" => $sid])->select("surveyls_survey_id,surveyls_title,surveyls_description")->asArray()->one();
        // 指定调研ID下的所有问题
        $questions = [];

        // 指定调研下所有的参与者
        $all_partners = $this->getUsersBySid($sid);

        $result = [
            "detail" => $LimeSurveyLanguageSetting,
            "all_partners" => $all_partners
        ];

        return $this->buildResponse('success', 201, $result);
    }

    /**
     * 根据调研ID获取答案选项
     * @param $sid
     * @param string $user_token
     * @return array
     */
    function getChooseBySid($sid, $user_token = '')
    {
        $questions = [];
        $LimeQuestions = LimeQuestions::find()->where(["sid" => $sid, "parent_qid" => 0])->select("qid,parent_qid,sid,gid,type,title,question,question_order")->orderBy("question_order asc,qid asc")->asArray()->all();
        if ($LimeQuestions) {
            foreach ($LimeQuestions as $k => &$v) {
                $choose = $this->getChooseBySidAndQidAndType($sid, $v['qid'], $v['type'], $user_token);
                $questions[] = $choose;
            }
        }
        return $questions;
    }

    /**
     * 获取答案选择项
     * @param $sid      调研ID
     * @param $qid      问题ID
     * @param $type     问题类型
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getChooseBySidAndQidAndType($sid, $qid, $type, $tokenString = '')
    {
        $groups = [];
        if (!$sid || !$qid || !$type) {
            return $groups;
        }

        $LS_BASEURL = 'http://survey.demo.dteols.cn/index.php/';
        $LS_USER = 'admin';
        $LS_PASSWORD = '20080807';
        $myJSONRPCClient = new JsonRPCClient($LS_BASEURL . '/admin/remotecontrol');
        $sessionKey = $myJSONRPCClient->get_session_key($LS_USER, $LS_PASSWORD);
        $map['token'] = 'zhuzf';
        $where = array("token", "firstname", "completed");
        $aQuestionSettings = array('question', 'answeroptions', 'available_answers', 'subquestions', 'attributes');
        $groups = $myJSONRPCClient->get_question_properties($sessionKey, $qid, $aQuestionSettings);

        if (!$groups) {
            return $groups;
        }
        $q = LimeQuestions::find()->where(["qid" => $qid])->select("gid,type,title")->asArray()->one();
        $groups['gid'] = $q['gid'];
        $groups['qid'] = $qid;
        $groups['type'] = $type;

        $keyname = $sid . "X" . $q['gid'] . "X" . $qid;
        $groups['keyname'] = $keyname;

        empty($tokenString) ? $condition = '' : $condition = ' where ' . $tokenString;

        $sql = ' SELECT * FROM lime_survey_' . $sid . $condition . ' order by id desc';

        $answers = \Yii::$app->db_survey->createCommand($sql)->queryAll();
        $a = [];
        foreach ($answers as $k => $v) {
            foreach ($v as $kk => $vv) {
                $n = substr_count($kk, $keyname);
                if ($n) {
                    if ($vv) {
                        $a[][$kk] = $vv;
                    }
                }
            }
        }
        $groups['answers'] = $a;

        switch ($type) {
            case "5":
                $groups['answeroptions'] = [];
                /* 五分选择题 */
                for ($i = 1; $i < 7; $i++) {
                    $val = $i;
                    if ($i == 6) {
                        $val = "拒答";
                    };
                    $groups['answeroptions'][$i] = (string)$val;
                }
                break;
            case "G":
                /* 性别 */
                $groups['answeroptions'] = [
                    "F" => "女",
                    "M" => "男",
                    "0" => "拒答",
                ];
                break;
            case "Y":
                /* 是否题 */
                $groups['answeroptions'] = [
                    "Y" => "是",
                    "N" => "否"
                ];
                break;

            default:
                if (!is_array($groups['answeroptions']) && is_array($groups['available_answers'])) {
                    $groups['answeroptions'] = $groups['available_answers'];
                    unset($groups['available_answers']);
                } else if ($groups['answeroptions'] && is_array($groups['answeroptions'])) {
                    $answers = [];
                    foreach ($groups['answeroptions'] as $k => $v) {
                        $answers[$k] = $v['answer'];
                    }
                    $groups['answeroptions'] = [];
                    $groups['answeroptions'] = $answers;
                }

                break;
        }

        $count = [];
        if (is_array($groups['answeroptions'])) {
            foreach ($groups['answeroptions'] as $k => &$v) {
                $val = [];
                foreach ($groups['answers'] as $k1 => $v1) {
                    foreach ($v1 as $k2 => $v2) {
                        switch ($groups['type']) {
                            case "G":
                                if ((string)$v2 == (string)$k) {
                                    $val[] = $v2;
                                }
                                break;
                            case "5":
                                if ((string)$v2 == (string)$k) {
                                    $val[] = $v2;
                                }
                                break;
                            case "Y":
                                if ((string)$v2 == (string)$k) {
                                    $val[] = $v2;
                                }
                                break;
                            case "O":
                                if ((string)$v2 == (string)$k) {
                                    $val[] = $v2;
                                }
                                break;
                            default:
                                if ($k2 == $keyname . $k) {
                                    $val[] = $v2;
                                }
                                break;
                        }
                    }
                }

                $count[$k] = $val;
            }
        }

        $groups['count'] = $count;
        unset($groups['attributes']);
        unset($groups['subquestions']);

        return $groups;
    }


    /**
     * 根据sid获取其下参与者列表，含头像等信息
     * @param $sid
     * @return array
     */
    public function getUsersBySid($sid)
    {
        $all_partners = [];
        $table_name = 'lime_tokens_' . $sid;
        $juge = \Yii::$app->db_survey->createCommand("show tables")->queryAll();
        $cun = $this->deep_in_array($table_name, $juge);
        if (!$cun) {
            return $all_partners;
        };
        $user = \Yii::loadService('user');
        $sql1 = 'SELECT tid,firstname,lastname,email,emailstatus,token,sent from ' . $table_name . ' order by tid asc';
        $all_partners = \Yii::$app->db_survey->createCommand($sql1)->queryAll();
        foreach ($all_partners as $k => &$v) {
            $u = $user->loadByCode($v['lastname']);
            if ($u['code'] == 201) {
                $v['user_id'] = $u['data']['id'];
                $v['user_avatar'] = $u['data']['avatar'];
                $v['safe_mobile'] = $u['data']['safe_mobile'];
                $v['user_name'] = $u['data']['user_name'];
            } else {
                $v['user_info'] = [];
            }
        }
        return $all_partners;
    }

    //判断二维数组是否存在值
    public function deep_in_array($value, $array)
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                if ($item == $value) {
                    return true;
                } else {
                    continue;
                }
            }

            if (in_array($value, $item)) {
                return true;
            } else if ($this->deep_in_array($value, $item)) {
                return true;
            }
        }
        return false;
    }
}