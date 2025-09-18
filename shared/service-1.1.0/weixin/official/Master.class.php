<?php
namespace service\weixin\official;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use \frontend\components\Component as ComComponent;
use \frontend\components\weixin\Jssdk as ComWeixinJssdk;

class Master extends ComComponent{
    
    public function __construct($config = []){
        parent::__construct();
    }
    
    public function getJssdkSign(){
        $wx_params    = \Yii::$app->params['weixin'];
        $com_jssdk    = new ComWeixinJssdk($wx_params['WX_APPID'], $wx_params['WX_APPSECRECT']);
        $sign_package = $com_jssdk->GetSignPackage();
        
        $sign_package['checking_url'] = $com_jssdk->getUrl();
        
        return $sign_package;
    }
}