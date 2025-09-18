<?php


namespace service\resource\models;


class RemoteFile extends \service\base\db\ARecord
{

    static public function tableName()
    {
        return 'remote_file';
    }
}