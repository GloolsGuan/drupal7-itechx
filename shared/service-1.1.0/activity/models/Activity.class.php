<?php


namespace service\activity\models;


class Activity extends \service\base\db\ARecord
{
    const STATUS_DELETED = -1;
    const STATUS_NORMAIl = 1;

    static public function tableName()
    {
        return 'activity';
    }

}