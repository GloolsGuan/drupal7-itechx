<?php


namespace service\survey\models;

use \service\base\db\SimpleAR;
use \service\base\Base;

class ARecord extends SimpleAR
{
    public static function getDb()
    {
        return Base::getCom('db_survey');
    }
}