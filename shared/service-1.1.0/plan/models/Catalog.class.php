<?php
/**
 * 课程体系(课程目录)模型定义
 */

namespace service\plan\models;

use \service\base\db\SimpleAR;


class Catalog extends SimpleAR
{
    
    
    public static function tableName()
    {
        return 'pl_catalog';
    }
    
	/**
     * 获取课程体系列表
     *
     * @param array $map 查询条件，格式参照yii2 where()方法
     * @param int $offset 数据偏移量
     * @param int $limit 条数限制
     * @return array 订单列表数据 统一格式
     * @author drce 20161123
     */
    public static function getItems(array $map = [], $offset = 0, $limit = 20, $order = '')
    {
        $query = static::find()->from(['catalog' => static::tableName()])
        ->select([
            'catalog.*'
        ])
        ->where($map);
    
        $catalogs = $query->offset($offset)->limit($limit)->orderBy('catalog.id DESC')->asArray()->all();
    
        return [$catalogs, $query->count()];
    }
    public static function getall(array $where,$offset = -1, $limit = -1)
    {

        $query = static::find()->from(['catalog' => static::tableName()])
            ->select([
                'catalog.*'
            ])
            ->where($where);

        $rows = $query->offset($offset)->limit($limit)->orderBy('catalog.id DESC')->asArray()->all();


        //var_dump($rows);
        return static::getTree($rows, 0, 'id', 'pid');
    }
    /**
     * 数组根据父id生成树
     * @staticvar int $depth 递归深度
     * @param array $data 数组数据
     * @param integer $pid 父id的值
     * @param string $key id在$data数组中的键值
     * @param string $chrildKey 要生成的子的键值
     * @param string $pKey 父id在$data数组中的键值
     * @param int $maxDepth 最大递归深度，防止无限递归
     * @return array 重组后的数组
     */
    public static function getTree($data, $pid = 0, $key = 'id', $pKey = 'pid', $childKey = 'child', $maxDepth = 0)
    {
        static $depth = 0;
        $depth++;
        if (intval($maxDepth) <= 0)
        {
            $maxDepth = count($data) * count($data);
        }
        if ($depth > $maxDepth)
        {
            //exit("error recursion:max recursion depth {$maxDepth}");
        }
        $tree = array();
        foreach ($data as $rk => $rv)
        {
            if ($rv[$pKey] == $pid)
            {
                $rv[$childKey] = static::getTree($data, $rv[$key], $key, $pKey, $childKey, $maxDepth);
                $tree[] = $rv;
            }
        }
        return $tree;
    }

}