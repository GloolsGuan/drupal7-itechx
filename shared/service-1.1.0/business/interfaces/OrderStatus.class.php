<?php
/**
 *
 */

namespace service\business\interfaces;

/**
 * Interface OrderStatus
 * 设置订单状态接口定义
 * @package service\business\interfaces
 * @design yangzy 20161128
 * @author shenf 20161128
 */
interface OrderStatus
{
    /**
     * 设置订单状态
     *
     * @param int $orderId 订单id
     * @param int $status 状态值
     * @return 成功时返回true [1]
     */
    public function setStatus($orderId, $status);
}