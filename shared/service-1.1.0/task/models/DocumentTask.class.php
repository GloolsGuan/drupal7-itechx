<?php


namespace service\task\models;


class DocumentTask extends \service\base\db\ARecord
{
    const TYPE = 3;

    static public function tableName()
    {
        return 'task_document';
    }
}