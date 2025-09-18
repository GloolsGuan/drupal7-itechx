<?php
namespace service\application\models;

class ApplicationBehavior extends \service\base\db\ARecord
{
    static public function tableName()
    {
        return 'application_behavior';
    }
}