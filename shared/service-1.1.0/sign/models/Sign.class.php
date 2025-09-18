<?php


namespace service\sign\models;

use service\base\Error;

use service\base\db\SimpleAR;


class Sign extends SimpleAR
{
    const STATUS_NORMAL = 1;
    const STATUS_DELETED = -1;

    public static function primaryKey()
    {
        return ['sign_id'];
    }

    public static function tableName()
    {
        return '{{%sign}}';
    }

    public function rules()
    {
        return [
            ['name', 'trim'],
            [['name'], 'required'],
            [['status'], 'default', 'value' => static::STATUS_NORMAL]
        ];
    }

    /**
     * 签到操作
     *
     * @param $data
     * @return bool|static
     */
    public function sign($data)
    {
        if (false === ($sign = SignDetail::createItem($data))) return false;

        return $sign;
    }

    public function getSignDetail()
    {
        return $details = SignDetail::find()->where(['sign_id' => $this->sign_id])->asArray()->all();
    }

    /**
     * 签到数据
     * @return \yii\db\ActiveQuery
     * @author yangzy 2017/2/28
     */
    public function getData()
    {
        return $this->hasMany(SignDetail::className(), ['sign_id' => 'sign_id']);
    }
}