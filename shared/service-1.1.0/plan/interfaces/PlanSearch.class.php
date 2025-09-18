<?php
/**
 *
 */

namespace service\plan\interfaces;

/**
 * 计划搜索接口
 *
 * @package service\plan\interfaces
 * @design yangzy 20161123
 * @author shenf 20161125
 */
interface PlanSearch
{
    /**
     * 搜索计划列表（要支持价格区间条件和所属课程目录(catalog)条件）
     *
     * @param array $map 搜索条件 格式参照yii2 where参数格式
     * @param int $offset 偏移
     * @param int $limit 数据量限制
     * @param array $order 排序 格式参照 yii2 order参数格式
     * @return array [1]
     */
    public function listPlans(array $map = [], $offset = 0, $limit = 20, array $order = []);
}