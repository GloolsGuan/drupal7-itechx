<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/28
 * Time: 9:43
 */

namespace service\survey\models;

use service\survey\models\LimeAnswers;

class LimeQuestions extends ARecord
{

    public static function tableName()
    {
        return 'lime_questions';
    }

    /**
     * 定义问题下的所有回答结果关系
     * @return \yii\db\ActiveQuery
     * @author qinjy 20170401
     */
    public function getAnswers()
    {
        return $this->hasMany(LimeAnswers::className(), ['qid' => 'qid']);
    }

    /**
     * 定义问题下子问题关系
     * @return \yii\db\ActiveQuery
     * @author qinjy 20170407
     */
    public function getChildQuestions()
    {
        return $this->hasMany(static::className(), ['parent_qid' => 'qid']);
    }
}