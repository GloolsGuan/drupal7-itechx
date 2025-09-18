<?php
namespace service\base;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use \service\base\Curl;

class ApiClient extends \service\base\Base
{
    
    public $api_uri = '';
    
    public $api_version = '';
    
    public $client_id = '';
    
    public $client_secret = null;
    
    public $client_expires = '';
    
    
    
    public function send($api, $params = array(), $method='GET') {
        $curl = new Curl();

        $_method = strtolower($method);
        if(method_exists($curl, $_method)) {
            $result = $curl->$_method($this->buildApiUri($api), json_encode($params, JSON_UNESCAPED_UNICODE), $method);
            //\GtoolsDebug::testLog(__FILE__, [$this->buildApiUri($api),json_encode($params, JSON_UNESCAPED_UNICODE),$result], __METHOD__);
            if (isset($result['status']) && 'failed'==$result['status']) {
                return $result;
            }
//            \GtoolsDebug::testLog(__FILE__, $result, __METHOD__);
            return $result;
        }
        
        throw new Exception(sprintf('Error, invalid API method provided.'));
    }
    
    
    
    protected function buildApiUri($api){
        return sprintf('%s%s', $this->api_uri, $api);
    }

    

    protected function _authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

        $ckey_length = 4;

        $key = md5($key ? $key : $this->uckey);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                    return substr($result, 26);
            } else {
                    return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
}