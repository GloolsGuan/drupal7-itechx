<?php


namespace service\sign\models;

use service\base\db\SimpleAR;

class SignDetail extends SimpleAR
{

    public static function tableName()
    {
        return '{{%sign_detail}}';
    }

    public function rules()
    {
        return [
            [['create_time'], 'default', 'value' => date('Y-m-d H:i:s', time())]
        ];
    }
}