<?php
namespace service\survey\models;

use service\survey\models\LimeQuestions;

class LimeSurvey extends ARecord
{
    public static function primaryKey()
    {
        return ['sid'];
    }

    public static function tableName()
    {
        return 'lime_surveys';
    }

    public static function getSurveys(array $map = [], $offset = -1, $limit = -1)
    {

        $where[] = 'AND';
        $where[] = ['active' => 'Y'];
        $query = static::find()->from(['l' => static::tableName()])
            ->select(['l.sid', 's.surveyls_survey_id', 's.surveyls_title'])
            ->where($where)
            ->andWhere($map)
            ->join('LEFT JOIN ', LimeSurveyLanguageSetting::tableName() . ' AS s', 'l.sid = s.surveyls_survey_id');
        $query->orderBy('datecreated desc');


        if (-1 != $offset) $query->offset($offset);
        if (-1 != $limit) $query->limit($limit);

        $list = $query->asArray()->all();
        $count = $query->count();

        return [$list, $count];
    }


    /**
     * 定义获取调研内容关系
     * @return \yii\db\ActiveQuery
     * @author qinjy 20170401
     */
    public function getSurveyContent()
    {
        return $this->hasOne(LimeSurveyLanguageSetting::className(), ['surveyls_survey_id' => static::primaryKey()[0]]);
    }

    /**
     * 定义调研下的所有问题关系
     * @return \yii\db\ActiveQuery
     * @author qinjy 20170401
     */
    public function getQuestions()
    {
        return $this->hasMany(LimeQuestions::className(), ['sid' => static::primaryKey()[0]]);
    }

}