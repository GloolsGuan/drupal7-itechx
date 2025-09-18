<?php
namespace service\weixin\official;


use service\weixin\Curl;

class Menu extends \service\base\Module
{
    protected $url_menu_get = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=ACCESS_TOKEN';

    protected $api_url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN';

    protected $api_web_safe_redirect = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect';

    /**
     * @var \service\weixin\official\Access
     */
    protected $access;

    /**
     * @return bool|\service\base\type|string
     */
    protected function getAccessToken()
    {
        if (empty($this->access))
            $this->access = $this->module->loadAccess();
        return $this->access->access();
    }

    /**
     * 获取当前的微信应用菜单设置
     */
    public function getMenu()
    {
        $url_menu_get = str_replace(['ACCESS_TOKEN'], [$this->getAccessToken()], $this->url_menu_get);

        $curl = new Curl();
        $content = $curl->get($url_menu_get);

        if ($content['code'] == 500)
            return $this->buildResponse('failed', 500, $content['data']);

        $result = json_decode($content['data'], true);

        if (array_key_exists('errcode', $result)) {
            return false;
        }

        return $result;
    }

    /**
     * 设置微信菜单
     * @param array $menu 菜单设置
     * @return bool|\service\base\type
     * @see http://mp.weixin.qq.com/wiki/10/0234e39a2025342c17a7d23595c6b40a.html
     */
    public function buildMenu(array $menu = [])
    {
        $access_token = $this->getAccessToken();

        $com_curl = new Curl();
        $api_url = str_replace('ACCESS_TOKEN', $access_token, $this->api_url);

        $content = $com_curl->post($api_url, json_encode($menu, JSON_UNESCAPED_UNICODE));

        if ($content['code'] == 500)
            return $this->buildResponse('failed', 500, $content['data']);

        $result = $content['data'];

        print_r($result);

        if (array_key_exists('errcode', $result)) {
            return false;
        }

        return true;
    }


    protected function inactiveMember()
    {
        $menu = array(
            'button' => array(
                array('name' => '首页', 'type' => 'view', 'key' => 'WX_MENU_HOME', 'url' => $this->buildUrl('http://afgj.glools.com')),
                array('name' => '订单', 'type' => 'view', 'key' => 'WX_MENU_ORDER', 'url' => $this->buildUrl('http://afgj.glools.com/order')),
                array('name' => '我', 'type' => 'view', 'key' => 'WX_MENU_PROFILE', 'url' => $this->buildUrl('http://afgj.glools.com/home')),
            )
        );

        ob_start();
        include(dirname(__FILE__) . '/menu.conf.inc');
        $menu = ob_get_clean();
        //GtoolsDebug::testLog(__FILE__, $menu, __METHOD__);
        return $menu;
    }


    protected function activeMemberLevel()
    {
        $menu = [
            'button' => [
                ['name' => '首页', 'type' => 'click', 'url' => 'https://www.glools.com/service'],
                ['name' => '订单', 'type' => 'click', 'url' => 'https://www.glools.com/service/order'],
                ['name' => '我的', 'type' => 'click', 'url' => 'https://www.glools.com/home/profile'],
            ]
        ];
        return json_encode($menu);
    }


    protected function buildUrl($url, $cache_state_key = '')
    {
        if (empty($cache_state_key)) {
            $cache_state_key = time();
        }

        $wx_web_safe_redirect = str_replace(['APPID', 'SCOPE', 'STATE'], [WX_APPID, 'snsapi_base', $cache_state_key], $this->api_web_safe_redirect);

        $real_url = str_replace('REDIRECT_URI', urlencode($url), $wx_web_safe_redirect);

        return $real_url;
    }
}