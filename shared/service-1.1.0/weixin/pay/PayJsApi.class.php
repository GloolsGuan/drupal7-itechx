<?php
/**
 *
 */

namespace service\weixin\pay;

use yii\helpers\ArrayHelper;
use service\base\Module;
use service\weixin\interfaces\pay\PayJsApi as PayJsApiInterface;

include_once __DIR__ . '/WxPay.JsApiPay.php';

class PayJsApi extends Module implements PayJsApiInterface
{
    public function createOrder(array $data)
    {
        include_once __DIR__ . '/source/lib/WxPay.Api.php';

        $input = new \WxPayUnifiedOrder();
        $input->SetBody(ArrayHelper::getValue($data, 'body', ''));
        $input->SetAttach(ArrayHelper::getValue($data, 'attach', ''));
        $input->SetOut_trade_no(\WxPayConfig::MCHID . date("YmdHis"));
        $input->SetTotal_fee(ArrayHelper::getValue($data, 'amount', 0));
        $input->SetTime_start(date("YmdHis"));
        $input->SetGoods_tag(ArrayHelper::getValue($data, 'goodstag', ''));
        $input->SetNotify_url(ArrayHelper::getValue($data, 'notifyurl', ''));
        $input->SetTrade_type("JSAPI");
        $input->SetProduct_id(ArrayHelper::getValue($data, 'orderids', ''));
//        $openId = 'o5rQ5uDGayB5Ostj4l_ZOryHhEF8';//yangzy openid
        $input->SetOpenid(ArrayHelper::getValue($data, 'openid', ''));

        $order = \WxPayApi::unifiedOrder($input);

        if (array_key_exists("return_code", $order)
            && array_key_exists("result_code", $order)
            && $order["return_code"] == "SUCCESS"
            && $order["result_code"] == "SUCCESS"
        ) {
            return $this->buildResponse('success', 201, $order);
        }
        return $this->buildResponse('error', 400, $order);
    }

    public function queryOrder($transactionId)
    {
        include_once __DIR__ . '/source/lib/WxPay.Notify.php';

        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transactionId);
        $result = \WxPayApi::orderQuery($input);

        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"
        ) {
            return $this->buildResponse('success', 201, $result);
        }
        return $this->buildResponse('error', 400, '订单查询错了');
    }
}