<?php
namespace service\weixin\enterprise;

use service\base\Cache;
use service\weixin\Curl;

class Access extends \service\base\Module
{

    static $api_url = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=CORP_ID&corpsecret=SECRET_KEY';

    static $userinfo_url = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=ACCESS_TOKEN&code=CODE';

    static $oauth_access_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=APPID&secret=SECRET&code=CODE&grant_type=authorization_code';

    static $access_token = '';

    static $expired_at = 0;

    /**
     * 获取accesstoken
     * @return bool|string
     */
    public function access()
    {


        if (false !== $this->accessFromCache()) {
            return $this->accessFromCache();
        }

        $com_curl = new Curl();
        $current_corp = $this->module->appid;
        $x_path_secret_key = $this->module->appsecret;
        $api_url = str_replace(['CORP_ID', 'SECRET_KEY'], [$current_corp, $x_path_secret_key], self::$api_url);

        $content = $com_curl->get($api_url);

//        return $content;
        if (isset($content['code']) && $content['code'] == 500)
            return $this->buildResponse('failed', 500, $content['data']);

        $result = json_decode($content['data'], true);

        if (array_key_exists('errcode', $result)) {
            return false;
        }


        self::$access_token = $result['access_token'];
        self::$expired_at = time() + $result['expires_in'];

        $this->cache([self::$access_token, self::$expired_at]);

//        echo self::$access_token . " access<br>";

        return $result['access_token'];
    }

    /**
     * threading level
     */
    public function accessFromCache()
    {
        list(self::$access_token, self::$expired_at) = $this->cache();

//        echo self::$access_token . " cache<br>";

        if (!empty(self::$access_token) && time() < self::$expired_at) {
            return self::$access_token;
        }

        return false;
    }

    public function getUserInfo($code)
    {
        $token = $this->access();

        $url = str_replace(['ACCESS_TOKEN', 'CODE'], [$token, $code], self::$userinfo_url);

        $curl = new Curl();
        $content = $curl->get($url);

        if (isset($content['code']) && $content['code'] == 500)
            return $content;

        $result = json_decode($content['data'], true);

        if (array_key_exists('errcode', $result)) {
            return $this->buildResponse('error', 400, $result['errmsg']);
        }

        return $result;
    }


    /**
     * @see http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html
     * @param type $code
     */
    public function getUserOpenId($code)
    {
        $content = $this->getOAuthAccessToken($code);

        //GtoolsDebug::testLog(__FILE__, [$code, $content], __METHOD__);

        if (array_key_exists('openid', $content)) {
            return $content['openid'];
        }

        return false;
    }


    public function getOAuthAccessToken($code)
    {

        $api_url = str_replace(['APPID', 'SECRET', 'CODE'], [$this->module->appid, $this->module->appsecret, $code], self::$oauth_access_token_url);
        $com_curl = new Curl();
        $content = $com_curl->get($api_url);

        //GtoolsDebug::testLog(__FILE__, $api_url, __METHOD__);

        if (isset($content['code']) && $content['code'] == 500)
            return false;

        return json_decode($content['data'], true);
    }


    private function checkSignature($signature, $timestamp, $nonce, $token = 'WX_TOKEN')
    {
        // you must define TOKEN by yourself
        if (empty($token)) {
            throw new Exception('TOKEN is not defined!');
        }

        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 设置/获取access_token
     * @param array $value 为空时表示从cache中获取，有值时表示设置，值的格式['access_token_值','expired_at_过期时间']
     * @return array|bool
     */
    protected function cache(array $value = [])
    {
        $cache = Cache::getCache();

        $salt = md5($this->module->appsecret);

        $key_token = 'access_token_' . $salt;
        $key_expired = 'expired_at_' . $salt;

        if (empty($value)) {
            return [$cache->get($key_token), $cache->get($key_expired)];
        }

        if (count($value) != 2) return false;

        $cache->set($key_token, $value[0]);
        $cache->set($key_expired, $value[1]);
        return true;
    }
}