<?php


namespace service\plan\models;

use service\base\db\SimpleAR;

class Signdata extends SimpleAR
{
    static public function tableName()
    {
        return 'sign_detail';
    }

    static public function primaryKey()
    {
        return ['id'];
    }

    public function rules()
    {
        return [
            ['sign_id', 'exist',
                'targetClass' => 'service\sign\models\Sign',
                'targetAttribute' => 'sign_id',
                'message' => 'sign was not found.'],
            ['create_time', 'default', 'value' => new Expression('NOW()')],
            [['unique_code'], 'string', 'length' => [64, 64]],
            [['user_name', 'longitude', 'latitude', 'address'], 'trim'],
            [['user_name', 'longitude', 'latitude'], 'required'],
        ];
    }

}