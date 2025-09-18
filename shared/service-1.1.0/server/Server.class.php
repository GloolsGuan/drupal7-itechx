<?php


namespace service\server;

use \service\base\Module;

class Server extends Module
{
    /**
     * 获取微信端访问地址
     * @return string
     */
    public function getWeisiteDomain()
    {
        return 'http://' . $_SERVER['HTTP_HOST'] . '/wx/wx/main';
    }
}