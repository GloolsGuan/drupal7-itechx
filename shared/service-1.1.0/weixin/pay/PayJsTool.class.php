<?php
/**
 *
 */
namespace service\weixin\pay;

use service\base\Module;
use service\weixin\interfaces\pay\PayJsTool as PayJsToolInterface;

include_once __DIR__ . '/WxPay.JsApiPay.php';

class PayJsTool extends Module implements PayJsToolInterface
{
    private $jsapiHandle;

    public function init()
    {
        parent::init();

        $this->jsapiHandle = new \JsApiPay();
    }

    public function makeJsApiParameters(array $order)
    {
        return $this->jsapiHandle->GetJsApiParameters($order);
    }

    public function getOpenid($redirectUrl = '')
    {
        return $this->jsapiHandle->GetOpenid($redirectUrl);
    }
}