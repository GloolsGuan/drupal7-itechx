<?php
namespace Gtools\Yii;
/* 
 * Extension of Yii2 request class for supporting weixin data interaction
 */

class Request extends \yii\web\Request{
    
    public static $mage_params = [];
    
    public function setMage_Params($params){
        self::$mage_params = \yii\helpers\ArrayHelper::merge(self::$mage_params, $params);
    }
    
    
    public function getMageParam($name){
        if(array_key_exists($name, self::$mage_params)){
            return self::$mage_params[$name];
        }
        
        return false;
    }
    
    
    public function getJsonPost(){
        $content = \file_get_contents('php://input');
        $post_state = (array) json_decode($content);
        //\Gtools\Debug::testLog(__METHOD__, $content, __LINE__);
        return $post_state;
    }
    
    
    public function createInternalUrl($url){
        $base_url = $this->getMageParam('base_url');
        $front_name = $this->getMageParam('front_name');
        return sprintf('%s/%s/%s', rtrim($base_url, '/'), ltrim($front_name, '/'), ltrim($url, '/'));
    }
}

