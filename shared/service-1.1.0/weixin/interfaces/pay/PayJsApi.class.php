<?php
/**
 *
 */

namespace service\weixin\interfaces\pay;

/**
 * Interface PayApi
 * 更详细的信息请参考微信支付官方demo
 * @package service\weixin\interfaces
 * @design yangzy 20161124
 * @author yangzy 20161124
 */
interface PayJsApi
{
    /**
     * 使用微信支付接口下订单
     *
     * @param array $data 订单数据 格式
     * [
     * 'body' => '商品或支付单简要描述',
     * 'attach' => '设置附加数据，在查询api和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据',
     * 'amount' => '设置订单总金额，只能为整数，详见支付金额',
     * 'goodstag' => '设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠',
     * 'notifyurl' => '设置接收微信支付异步通知回调地址',
     * 'openid' => '用户openid'
     * ]
     * ,其中：[body，amount，notifyurl，openid]不能为空
     * @return array [1]
     */
    public function createOrder(array $data);

    /**
     * 根据订单号查询订单详情
     *
     * @param string $transactionId 订单号
     * @return array 订单详情 [1]
     */
    public function queryOrder($transactionId);
}