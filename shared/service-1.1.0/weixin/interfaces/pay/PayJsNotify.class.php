<?php
/**
 *
 */

namespace service\weixin\interfaces\pay;

/**
 * Interface Notify
 * 用于接收微信支付回调返回的数据
 *
 * 更详细的信息请参考微信支付官方demo
 *
 * @package service\weixin\interfaces
 * @design yangzy 20161124
 * @author yangzy 20161124
 */
interface PayJsNotify
{
    /**
     * 获取微信支付回调接口返回的数据
     *
     * @return array [1]
     */
    public function getNotifyData();
}