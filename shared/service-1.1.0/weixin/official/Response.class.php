<?php
namespace service\weixin\official;

/*
 * Response custom message to weixin user
 */


class Response extends \service\base\Module
{

    protected $push_api = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=ACCESS_TOKEN';

    protected static $access_token = null;

    protected static $com_curl = null;

    public function __construct($config = [])
    {

        $com_weixin_access = $this->module->loadAccess();
        self::$access_token = $com_weixin_access->access();
        self::$com_curl = new ComCurl();

        parent::__construct($config);
    }


    public function push($message)
    {

        $url = str_replace('ACCESS_TOKEN', self::$access_token, $this->push_api);
        $_raw_response = self::$com_curl->post($url, $message);
        $_result = substr($_raw_response, strrpos($_raw_response, "\n"));
        $result = json_decode($_result, true);

        if (0 === $result['errcode'] && 'ok' == $result['errmsg']) {
            return $this->buildResponse('success', 200, '');
        }

        return $this->buildResponse('failed', 501, $result);
    }
}