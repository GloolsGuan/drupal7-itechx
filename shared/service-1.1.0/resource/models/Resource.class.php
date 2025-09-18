<?php
namespace service\resource\models;

use service\base\db\SimpleAR;

class Resource extends SimpleAR
{
    public static function primaryKey()
    {
        return ['resource_id'];
    }

    static public function tableName()
    {
        return 'resource';
    }

    public static function statusKey()
    {
        return 'resource_status';
    }

    public function rules()
    {
        return [
            ['resource_status', 'default', 'value' => static::STATUS_NORMAL],
        ];
    }

    public function attributeLabels()
    {
        return [
            'resource_name' => '名称',
            'type' => '类型',
            'create_time' => '创建时间',
        ];
    }

    static public function getResources(array $where = [])
    {
        return self::find()->from(['re' => self::tableName()])
            ->select(['re.*', 'tags.*'])
            ->join('LEFT JOIN',
                "(SELECT GROUP_CONCAT(tag.tag_name) AS tags,rs.resource_id AS re_id,GROUP_CONCAT(tag.resource_tag_id) AS tag_ids FROM " . RsResourceTag::tableName() . " rs LEFT JOIN " . ResourceTag::tableName() . " AS tag ON rs.resource_tag_id = tag.resource_tag_id GROUP BY rs.resource_id) AS tags",
                'tags.re_id = re.resource_id')
            ->where($where)->asArray()->all();
    }

    /*
     * 添加方法调用
     * */
    public static function getMaterialDetail($id,$plan_id)
    {
        if(empty($plan_id)) return [];
        $sql = '';
        $sql .= ' SELECT l.*,t.* FROM '.static::tableName().' l LEFT JOIN lime_tokens_'.$plan_id.' t ON 1=1 WHERE t.lastname="'.$id.'" AND l.resource_resource_id = '.$plan_id.' UNION';
        $sql = substr($sql,0,-5);
//        return $sql;

        $material = \Yii::$app->db_resource->createCommand($sql)->queryAll();

        return $material;
    }
}