<?php
/**
 * 文章服务
 *
 */

namespace service\article;

use \service\article\models\Artcile as ModArticle;
use service\base\Base;

/**
 *
 * 文章服务类
 * Class Article
 *
 * @package service\article
 */
class Article extends \service\base\Module
{
    public function __construct($id, $parent = null, $config = [])
    {
        parent::__construct([]);
    }

    /**
     * 新建文章
     *
     * @param array $data 文章的数据
     * @example a createArticle(['title'=>'文章标题','creator'=>'创建人'])
     * @return bool
     */
    public function createArticle(array $data = [])
    {
        return ModArticle::instance()->setAttributes($data)->save();
    }

    /**
     * 更新文章
     *
     * @param array $data 文章数据,id是必须的
     * @return bool
     */
    public function updateArticle(array $data = [])
    {
        if (!isset($data['id']) || empty($data['id'])) return false;

        return ModArticle::updateArticle($data);
    }

    /**
     * 删除文章（标记文章状态为删除）
     *
     * @param array|int $ids 单个文章的ID或ID组成的数组
     * @return bool|int
     */
    public function deleteArticles(array $ids = [])
    {
        return ModArticle::deleteArticlesById($ids);
    }

    /**
     * （从回收站中）还原文章
     *
     * @param array|int $ids 单个文章的ID或ID组成的数组
     * @return bool|int
     */
    public function restoreArticles(array $ids = [])
    {
        return ModArticle::restoreArticlesById($ids);
    }

    /**
     * 获取文章列表
     *
     * @param array $map 过滤条件
     * @param int $offset 偏移量
     * @param int $limit 获取条数
     * @return array 文章数据组成的二维数组
     */
    public function getArticles(array $map = [], $offset = 0, $limit = 20)
    {

        if (!isset($map['status'])) {
            $map['status'] = ModArticle::STATUS_NORMAL;
        }
        $map = [$map];
        array_unshift($map, 'AND');
//        return $map;
        return ModArticle::listArticles($map, $offset, $limit);
    }

    /**
     * 加载文章的标签服务
     *
     * @author yangzy
     * @email yangzy@dailyedu.com
     * @return \service\article\Tag 文章的标签服务
     */
    public function loadTag()
    {
        return Base::loadService('\service\article\Tag', [], $this);
    }

    /**
     * 查询文章详细信息
     *
     * @param $id 文章的ID
     * @param bool $asArray 为false时，返回\service\base\db\SimpleAR对象，否则，返回数组格式
     * @return array|\service\base\db\SimpleAR 文章详细信息|文章的SimpleAR对象
     */
    public function getArticleById($id, $asArray = true)
    {
        $article = ModArticle::getArticleById($id);

        if (empty($article)) return $this->buildResponse('error', 400, 'article was not found');

        if ($asArray) $article = $article->getAttributes();

        return $article;
    }

}