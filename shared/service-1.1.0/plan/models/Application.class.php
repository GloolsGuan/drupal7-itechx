<?php


namespace service\plan\models;

class Application extends \service\base\db\ARecord
{

    static public function tableName()
    {
        return 'Application';
    }

    static public function primaryKey()
    {
        return ['id'];
    }



}