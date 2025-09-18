<?php
/**
 *
 */

namespace service\widgets\models;

use service\base\db\ARecord;
use yii\base\Model;
use service\widgets\models\search\WidgetSearch;

/**
 * Class Widget
 * @package services\widgets\models
 * @author yangzy 2017/3/22
 */
class Widget extends ARecord
{
    public static function tableName()
    {
        return '{{%widget}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['title', 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '插件标题',
            'master_title' => 'class名称和namespace',
            'master_code' => '64位hash值',
            'mode' => '模板文件的前缀名称',
            'logo' => '后台LOGO',
            'icon' => '前台LOGO',
            'author' => '开发者名称',
            'author_url' => '开发者信息',
            'predefined_properties' => '预定义插件属性',
        ];
    }
    
}