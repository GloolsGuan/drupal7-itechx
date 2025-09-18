<?php


namespace service\task\models;


class Task extends \service\base\db\ARecord
{
    const STATUS_DELETED = -1;
    const STATUS_NORMAL = 1;

    static public function tableName()
    {
        return 'task';
    }
}