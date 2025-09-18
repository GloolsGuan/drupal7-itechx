<?php
/**
 *
 */

namespace service\weixin\interfaces\pay;

/**
 * Interface Tool
 * 更详细的信息请参考微信支付官方demo
 * @package service\weixin\interfaces
 * @design yangzy 20161124
 * @author yangzy 20161124
 */
interface PayJsTool
{
    /**
     * 使用支付接口返回的订单数据生成供jsapi接口使用的参数
     *
     * 使用示例：
     * WeixinJSBridge.invoke(
     * 'getBrandWCPayRequest',
     * <?php echo $jsApiParameters; ?>,
     * function(res){}
     * );
     *
     * @param array $order 支付接口返回的订单数据
     * @return array jsapi所需参数
     */
    public function makeJsApiParameters(array $order);

    /**
     *
     * 通过跳转获取用户的openid，跳转流程如下：
     * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
     * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
     *
     * @return 用户的openid
     */
    public function getOpenid();
}