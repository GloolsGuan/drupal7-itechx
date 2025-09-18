<?php
namespace Gtools;


class Curl{
    protected $headers = [];
    public $user_agent = '';
    public $compression = '';
    public $cookie_file = '';
    public $cookies = [];
    public $proxy = null;

    public function __construct($cookies=FALSE,$cookie='cookies.txt',$compression='gzip',$proxy='') {
        $this->headers[] = 'Connection: keep-alive';
        $this->headers[] = 'Content-Type: application/json; encoding=utf-8';
        //$this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
        //$this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)';

        $confs = [
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'compression' => $compression,
            'cookie_file' => $cookie,
            'proxy' => $proxy,
            'cookies' => $cookies
        ];
        
        foreach($confs as $k=>$v){
            if (property_exists($this, $k)){
                $this->$k = $v;
            }
        }
    }
    
    
    public function setHeader($name, $value){
        $this->headers[] = sprintf('%s: %s', $name, $value);
        return $this->headers;
    }


    public function setCookies($set_cookies){
        if (true==$set_cookies) {
            $this->_setCookies($this->cookie_file);
        }
    }


    protected function _setCookies($cookie_file) {
        if (file_exists($cookie_file)) {
            $this->cookie_file=$cookie_file;
        } else {
            $fp = fopen($cookie_file,'w') or $this->error('The cookie file could not be opened. Make sure this directory has the correct permissions');
            $this->cookie_file=$cookie_file;
            fclose($fp);
        }
    }


    public function get($url, $params=array()) {
        $query = '';
        if (!empty($params)) {
        	$query = $this->buildQuery($params);
        }

        $process = curl_init($url . $query);
        curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);//TODO Curl证书处理
        curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt($process,CURLOPT_ENCODING , $this->compression);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);

        return $this->buildResponse(curl_exec($process), $process, $url, $params);
    }



    public function post($url,$data) {
        $process = curl_init($url);

        curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($process, CURLOPT_HEADER, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);//TODO Curl证书处理
        curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
        curl_setopt($process, CURLOPT_ENCODING , $this->compression);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        //if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($process, CURLOPT_POST, 1);

        
        return $this->buildResponse(curl_exec($process), $process, $url, $data);
    }

    public function postFile($url,$data) {
        $process = curl_init($url);
        curl_setopt ( $process, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($process, CURLOPT_POST, 1);
        //curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);  //添加此选项 保存
        curl_setopt($process, CURLOPT_HEADER, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);//TODO Curl证书处理
        curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
        if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
        curl_setopt($process, CURLOPT_ENCODING , $this->compression);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        //if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, $this->proxy);
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);

        return $this->buildResponse(curl_exec($process), $process, $url, $data);
    }

    protected function buildResponse($content, &$process, $uri='', $params=null){
        //\Gtools\Debug::testLog(__FILE__, $content, __METHOD__);
        if (false==$content) {
            $response = array(
                'status' => 'failed',
                'code' => 500,
                'data' => [
                    'curl_error' => curl_error($process),
                    'request_uri' => $uri,
                    'params' => $params
                ]
            );
            curl_close($process);
            return $response;
        }

        if(false!==($co_start=strrpos($content, "\r\n\r\n"))) {
            //\GtoolsDebug::testLog(__FILE__, $co_start, __METHOD__);
            $content = trim(substr($content, $co_start));
        }
        
        //-- 299 means, I dont know what returned or there is nothing returned.
        $decode_content = json_decode($content, true);
        //\Gtools\Debug::testLog(__FILE__, [$content, $decode_content], __METHOD__);
        curl_close($process);
        
        if (empty($decode_content) || false==$decode_content){
            return $content;
        }
        
        return $decode_content;
    }


    public function buildQuery($data){
    	if (!is_array($data) || empty($data)) {
    		return '';
    	}

        if (is_string($data)) {
        	return $data;
        }

        $q = array();
        foreach($data as $k=>$v) {
        	$q[] =urlencode($k) . '=' . urlencode($v);
        }

        return '?' . implode('&', $q);
    }
}