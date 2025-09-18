<?php


namespace service\task\models;


class SurveyTask extends \service\base\db\ARecord
{
    const TYPE = 2;

    static public function tableName()
    {
        return 'task_survey';
    }
}