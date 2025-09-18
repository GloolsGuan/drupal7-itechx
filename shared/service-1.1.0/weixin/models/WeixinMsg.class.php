<?php
/**
 *
 */

namespace service\weixin\models;

use service\base\db\ARecord;
use yii\base\Model;

/**
 * Class WeixinMsg
 * @package services\weixin\models
 * @author wj 2017/4/7
 */
class WeixinMsg extends ARecord
{
    public static function tableName()
    {
        return '{{%weixin_msg}}';
    }

    

}