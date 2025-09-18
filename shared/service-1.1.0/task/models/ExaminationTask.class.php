<?php


namespace service\task\models;


use service\base\Base;
use service\base\Error;

class ExaminationTask extends \service\base\db\ARecord
{
    const TYPE = 1;

    static public function tableName()
    {
        return 'task_examination';
    }
}