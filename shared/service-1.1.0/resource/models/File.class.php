<?php


namespace service\resource\models;


class File extends \service\base\db\ARecord
{

    static public function tableName()
    {
        return 'file';
    }
}