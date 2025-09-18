<?php


namespace service\exam\models;

use \service\base\db\SimpleAR;
use \service\base\Base;

class ARecord extends SimpleAR
{
    public static function getDb()
    {
//        return Base::getCom('db_survey');
        return \Yii::$app->db_survey;
    }
}