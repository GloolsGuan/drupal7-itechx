<?php


namespace service\article\models;


class Artcile extends \service\base\db\ARecord
{
    /**
     * 删除状态
     */
    const STATUS_DELETED = -1;

    /**
     * 正常状态
     */
    const STATUS_NORMAL = 1;


    public static function tableName()
    {
        return 'article';
    }

    public function rules()
    {
        return [
            [['creator'], 'required'],
            ['status', 'default', 'value' => self::STATUS_NORMAL],
            [['create_time', 'update_time'], 'default', 'value' => date('Y-m-d H:i:s', time())]
        ];
    }

    public static function getArticle($where)
    {
        return self::find()->where($where)->one();
    }

    public static function getArticleById($id)
    {
        return self::getArticle(['id' => $id]);
    }

    public static function listArticles(array $where = [], $offset, $limit)
    {
        $articles = self::find()->where($where)->asArray()->offset($offset)->limit($limit)->all();
        $count = self::find()->where($where)->count();
        return [$articles, $count];
    }

    protected static function updateArticles(array $data = [], array  $where = [])
    {
        return self::updateAll($data, $where);
    }

    public static function deleteArticlesById($id)
    {
        if (!is_array($id)) $id = [$id];

        if (empty($id)) return false;

        return self::updateArticles(['status' => self::STATUS_DELETED, 'update_time' => date('Y-m-d H:i:s', time())], ['id' => $id]);
    }

    public static function restoreArticlesById($id)
    {
        if (!is_array($id)) $id = [$id];

        if (empty($id)) return false;

        return self::updateArticles(['status' => self::STATUS_NORMAL, 'update_time' => date('Y-m-d H:i:s', time())], ['id' => $id]);
    }

    public static function saveArticle(self $article)
    {
        return $article->save();
    }

    public static function instance()
    {
        return new self();
    }

    public static function updateArticle(array $data = [])
    {
        $data['update_time'] = date('Y-m-d H:i:s', time());

        return self::getArticleById($data['id'])->setAttributes($data)->save();
    }
}