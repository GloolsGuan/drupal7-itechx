<?php
namespace service\resource\models;


class ResourceTag extends \service\base\db\ARecord
{
    static public function tableName()
    {
        return 'resource_tag';
    }

    static public function getTags(array $where = [])
    {
        return self::find()->asArray()->all();
    }

    static public function addTags(array $tag_names = [])
    {
        if (!is_array($tag_names)) $tag_names = [$tag_names];
        if (empty($tag_names)) return true;
        $ex_tags = self::find()->where(['tag_name' => $tag_names])->asArray()->all();
        $ex_tag_names = array_column($ex_tags, 'tag_name');
        $diff_names = array_diff($tag_names, $ex_tag_names);
        array_walk($diff_names, function (&$item) {
            $item = [$item];
        });
        if (!empty($diff_names)) {
            self::find()->createCommand()->batchInsert(self::tableName(), ['tag_name'], $diff_names)->execute();
        }
        $tags = self::find()->where(['tag_name' => $tag_names])->asArray()->all();
        return $tags;
    }
}