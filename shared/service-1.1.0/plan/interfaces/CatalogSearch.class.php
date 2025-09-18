<?php
/**
 *
 */

namespace service\plan\interfaces;

/**
 * Interface CatalogSearch
 * 课程搜索接口
 * @package service\plan\interfaces
 * @design yangzy 20161128
 * @author shenf 20161129
 */
interface CatalogSearch
{
    /**
     * 搜索课程
     * @param array $map 搜索条件 格式参照yii2 where方法的参数
     * @return 成功时返回匹配到的课程组成的列表 [1]
     */
    public function search(array $map = []);
}