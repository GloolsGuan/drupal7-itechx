<?php
/**
 *
 */

namespace service\widgets\models;

use service\base\db\ARecord;
use yii\base\Model;

/**
 * Class WidgetInstance
 * @package services\widgets\models
 * @author yangzy 2017/3/22
 */
class WidgetInstance extends ARecord
{
    public static function tableName()
    {
        return '{{%widget_instance}}';
    }

    public $title;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'widget_id','master_ext_id'], 'integer'],
            ['widget_id', 'required']
        ];
    }

    public function getWidget()
    {
        /**
         * 第一个参数为要关联的字表模型类名称，
         *第二个参数指定 通过子表的 customer_id 去关联主表的 id 字段
         */
        return $this->hasMany(Widget::className(), ['id' => 'widget_id']);
    }

}