<?php
/**
 * 订单处理
 */

namespace service\business\interfaces;

/**
 * 订单处理接口定义
 *
 * 创建订单时标记订单状态为删除，向订单中添加商品时将订单订单标记为正常状态，这样是为了避免产生空商品的订单
 *
 * 订单状态：，-2:还处于购物车状态中，-1：取消，0：待支付，1：已支付，2：待审核，3：审核通过，4：审核未通过
 * 商品状态：同订单状态
 *
 * @package service\business\interfaces
 * @design yangzy 20161121
 * @author shenf 20161122
 */
interface Order
{
    /**
     * 创建订单
     *
     * @param array $order 订单详情，数据字段参照数据表biz_order
     * @return array 返回新创建的订单数据，统一数据格式
     */
    public function createOrder(array $order = []);

    /**
     * 查询订单详情
     *
     * @param int $id 订单id
     * @return array 订单详情 统一格式
     */
    public function getOrder($id);

    /**
     * 获取订单列表
     *s
     * @param array $map 查询条件，格式参照yii2 where()方法
     * @param int $offset 数据偏移量
     * @param int $limit 条数限制
     * @return array 订单列表数据 统一格式
     */
    public function getOrders(array $map = [], $offset = 0, $limit = 20);

    /**
     *统计订单数
     *
     * @param array $map 约束条件，格式参照yii2 where()方法
     * @return array 订单数 统一格式
     */
    public function countOrders(array $map = []);

    /**
     * 向订单中添加商品
     *
     * @param int $orderId 订单id
     * @param array $good 商品详情 数据格式参照biz_order_goods表
     * @return 返回添加后的商品数据，统一数据格式
     */
    public function addGood($orderId, array $good = []);

    /**
     * 从订单中删除指定的商品
     *
     * @param int $orderId 订单id
     * @param int $orderGoodId 订单商品id
     * @return boolean|array 成功返回true [1]
     */
    public function deleteGood($orderId, $orderGoodId);

    /**
     * 查询订单中的商品
     *
     * @param int $orderId 订单id
     * @return array 商品列表
     */
    public function getGoods($orderId);

    /**
     * 审核订单，标记订单状态
     *
     * @param int $orderId 订单id
     * @param bool $approved 是否通过审核
     * @return array 统一格式
     */
    public function auditOrder($orderId, $approved = true);

}