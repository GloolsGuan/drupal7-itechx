<?php
/**
 *
 */

namespace service\business\interfaces;

/**
 * Interface RefundGood
 *
 * @package service\business\interfaces
 * @design yangzy 20161130
 * @author shenf 20161201
 */
interface RefundGoods
{
    /**
     * 向退单申请中添加已购买到的产品
     * 只有已购买到的商品才能被添加成退单申请中
     * 添加商品的时候同时要更改订单商品表中的商品的状态为退费中
     *
     * @param int $refundId 退单id
     * @param int|array $orderGoodId 订单商品表中的id 可以为数字或数组
     * @return 成功时返回true [1]
     */
    public function addGood($refundId, $orderGoodId);

    /**
     * 从退单申请中移除要退的商品
     * 删除商品的时候同时要更改订单商品表中的商品的状态为审核通过
     *
     * @param int $refundId 退单申请的id
     * @param int|array $refundGoodId 退单商品表中的id 可以为数字或数组
     * @return 成功时返回true [1]
     */
    public function removeGood($refundId, $refundGoodId);

    /**
     * 获取退单申请中包括的商品
     * @param int $refundId 退单申请的id
     * @return 成功时返回商品列表 [1]
     */
    public function getRefundGoods($refundId);

}