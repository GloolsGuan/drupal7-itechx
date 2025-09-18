<?php
/**
 *退单处理
 */

namespace service\business\interfaces;

/**
 * 退单接口定义
 * Interface Refund
 *
 * @package service\business\interfaces
 * @design yangzy 20161121
 * @author shenf 20161123
 */
interface Refund
{
    /**
     * 创建退单申请
     * @param array $refund 申请详情，数据字段参照数据表biz_refund
     * @return mixed
     */
    public function createRefund(array $refund = []);

    /**
     * 查询退单申请详情
     *
     * @param int $id 申请id
     * @return array 申请详情 统一格式
     */
    public function getRefund($id);

    /**
     * 获取退单列表
     *
     * @param array $map 查询条件，格式参照yii2 where()方法
     * @param int $offset 数据偏移量
     * @param int $limit 条数限制
     * @return array 退单列表数据 统一格式
     */
    public function getRefunds(array $map = [], $offset = 0, $limit = 20);


    /**
     *统计退单数
     *
     * @param array $map 约束条件，格式参照yii2 where()方法
     * @return array 退单数 统一格式
     */
    public function countRefunds(array $map = []);


    /**
     * 审核退单，标记退单状态，同时更新退单商品状态和订单商品状态
     *
     * @param int $refundId 退单id
     * @param bool $approved 是否通过审核
     * @return array 统一格式
     */
    public function auditRefund($refundId, $approved = true);

}