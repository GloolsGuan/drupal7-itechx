<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/30
 * Time: 15:14
 */

namespace service\resource\models;


class RsResourceTag extends \service\base\db\ARecord
{
    static public function tableName()
    {
        return 'rs_resource_tag';
    }

    static public function setTags(array $tags = [], $resource_id)
    {
        if (empty($tags)) return false;
        $data = [];
        foreach ($tags as $tag) {
            $data [] = [$resource_id, $tag['resource_tag_id']];
        }
        self::deleteAll(['resource_id' => $resource_id]);
        return self::find()->createCommand()->batchInsert(self::tableName(), ['resource_id', 'resource_tag_id'], $data)->execute();
    }
}