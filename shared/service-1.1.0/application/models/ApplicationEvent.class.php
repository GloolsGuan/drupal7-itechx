<?php


namespace service\application\models;


class ApplicationEvent extends \service\base\db\ARecord
{
    static public function tableName()
    {
        return 'application_event';
    }


    
    static public function appEventList(){
        return static::find()
            ->orderBy('id DESC')
            ->asArray()
            ->all();
    }
}