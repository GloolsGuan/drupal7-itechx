<?php
namespace Ebouti\Weixin;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class User extends \Gtools\Yii\Module{
    
    public $title = '';
    
    public $APPID = '';
    
    public $APPSECRET = '';
    
    public $api_get_openid = "https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=APPSECRET&js_code=JSCODE&grant_type=authorization_code";
    
    public function __construct($config = array()) {
        parent::__construct($config);
    }
    
    
    public function getOpenId($login_code){
        $request_uri = str_replace(['APPID', 'APPSECRET', 'JSCODE'], [$this->APPID, $this->APPSECRET, $login_code], $this->api_get_openid);
        
        $api_response = \Zend\Http\ClientStatic::get($request_uri);
        $re = \GuzzleHttp\json_decode($api_response->getContent(), true);
        if (array_key_exists('errorcode', $re)) {
            return $this->buildResponse(400, 'error', $re);
        }
        
        return $this->buildResponse(201, 'success', $re);
    }
}