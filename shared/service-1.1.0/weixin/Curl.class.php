<?php


namespace service\weixin;


class Curl extends \service\base\Curl
{

    public function buildResponse($content, &$process, $uri = '', $params = null)
    {
        $res = parent::buildResponse($content, $process, $uri, $params);

        if (isset($res['code']) && $res['code'] == 500)
            return $this->buildResponse('error', $res['code'], $res['data']);

        return ['data' => json_encode($res, JSON_UNESCAPED_UNICODE)];
    }
}
