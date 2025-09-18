<?php


namespace service\file\models;
use yii\db\Expression;

class File extends \service\base\db\ARecord
{

    static public function tableName()
    {
        return '{{%file}}';
    }

    public function rules()
    {
        return [
            [['filename', 'savename', 'savepath', 'md5', 'sha1'], 'trim'],
            ['createtime', 'default', 'value' => new Expression('NOW()')],
        ];
    }
}