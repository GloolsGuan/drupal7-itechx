<?php
namespace Gtools;
/*
 * GTools Library: Debug
 *
 *
 * Created at: 2014-12-01
 * Maintainer: GloolsGuan, GloolsGuan@glools.com
 *
 * 
 * Public Methods:
 * # Special for input information listening
 * Lib_Gtools_Debug::sysLogInput($positionMark, $title);
 * 
 * # For system level debug
 * Lib_Gtools_Debug::sysLog($positionMark, $data, $title);
 *
 * # For developer debug, "private" level
 * Lib_Gtools_Debug::priLog($positionMark, $data, $title='');
 * 
 *
 * APACHE virtial host settings for enable the Lib_Dongli_Debug
 *   - SetEnv APPLICATION_ENV "development" | "test" | "life-server"
 *
 * 
 * Default logs directory setting depend on the global constant "OC_DIR_LOGS"
 *   - defined('OC_DIR_LOGS')    || define('OC_DIR_LOGS', OC_ROOT . DS . 'logs');
 */

/**
 * Description of Debug
 *
 * @author GloolsGuan
 */
class Debug {
    //put your code here
    protected static $level        ='';

    protected static $env          = '';

    protected static $recordSource = NULL;

    protected static $mode         = 'file';

    protected static $clientInfo   = array();

    protected static $filePath     = '';



    public static function init(){
        self::$clientInfo = array(
            'ip'        => $_SERVER['REMOTE_ADDR'],
            'userAgent' => $_SERVER['HTTP_USER_AGENT'],
            'client'    => self::parseClient(),
            'user'      => substr($_SERVER['REMOTE_ADDR'], strrpos($_SERVER['REMOTE_ADDR'], '.')+1)
        );

        if (!defined('DIR_LOGS')) {
            throw new \Exception('System Error: constant DIR_LOGS must be defined before you run the class ' . __CLASS__);
        }

        self::$filePath = DIR_LOGS ;

        ini_set('date.timezone', 'Asia/Shanghai');
    }

    public static function sysLogInput($positionMark, $title=''){

        $content = array();

        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if ('HTTP_' == substr($key, 0, 5)) {
                $headers[str_replace('_', '-', substr($key, 5))] = $value;
            }
        }
        $content['http-header'] = $headers;

        $content['request path'] = $_SERVER["PATH_INFO"];
        $content['query'] = $_SERVER["REDIRECT_QUERY_STRING"];
        $content['get']   = $_POST;
        $content['input'] = file_get_contents('php://input');

        self::sysLog($positionMark, $content, $title);
    }

    public static function sysLog($positionMark, $data, $title=''){
        self::record('system', $positionMark, $data, $title);
    }

    public static function testLog($positionMark, $data, $title=''){
        self::record('test', $positionMark, $data, $title);
    }
    
    public static function ucLog($positionMark, $data, $title=''){
        self::record('uc', $positionMark, $data, $title);
    }
    
    public static function Log($positionMark, $data,$name='debug',$title=''){
        self::record($name, $positionMark, $data, $title);
    }

    public static function priLog($positionMark, $data, $title=''){
        self::record('private', $positionMark, $data, $title);
    }


    protected static function record($level='private', $positionMark, $data, $title=''){
        self::init();
        //-- DEBUG enviroment must be set in apache --
        //If on "test" enviroment, All private log will be turn off.
        $enable = defined('GTOOLS_DEBUG') ? GTOOLS_DEBUG : false;

        if (!self::enableDebug($enable)) {
            return false;
        } else if ('test'==self::$env && 'system'!=$level) {
            return false;
        }
        $header = array();
        $time = date('Y-m-d H:i:s', time());
        $title = empty($title) ? '' : ' - ' . $title;

        if (file_exists($positionMark)) {
            $filePathInfo = pathinfo($positionMark);
            $header[] = '[' . $time . ']  ' . $filePathInfo['basename'] . $title;
            $header[] = $positionMark;
        } else {
            $header[] = '[' . $time . ']  ' . $positionMark . $title;
        }

        $prefix = "\n * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * \n ";

        if (!empty(self::$clientInfo['client'])) {
            $header[] = self::$clientInfo['client'] . ' @ ' . self::$clientInfo['ip'];
        }else{
            $header[] =  self::$clientInfo['ip'];
        }

        if (!empty(self::$clientInfo['userAgent'])) {
            $header[] = self::$clientInfo['userAgent'];
        }


        if ('private'==$level){
                $logUser = self::$clientInfo['user'];
        }
        else {
                $logUser = $level;
        }

        /*
        if('system'==$level){
            $logUser = 'system';
        } else {
            $logUser = self::$clientInfo['user'];
        }
        */
        $suffix = "\n - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - \n";
        if ('database'==self::$mode) {
            self::recordDataToDB($level, $header, $data, $logUser);
        } else {
            $buildHeader = $prefix . implode(" \n ", $header) . $suffix;
            self::recordDataToFile($level, $buildHeader, $data, $logUser);
        }
    }

    protected static function enableDebug($enable=false){
        if (!empty($_SERVER['APPLICATION_ENV'])
                && ('development'==$_SERVER['APPLICATION_ENV'] || 'test'==$_SERVER['APPLICATION_ENV'])) {
            self::$env = $_SERVER['APPLICATION_ENV'];
            return true;
        }

        if (true===$enable) {
            return true;
        }

        return false;
    }


    protected static function parseClient(){
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        /* 
        $userAgent = array(
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0',
            'Dalvik/1.6.0 (Linux; U; Android 4.4.4; Galaxy Nexus Build/KTU84Q)',
            'PowerCRM/0.2.4 (iPhone Simulator; iOS 8.1; Scale/2.00)'
        );
        */
        preg_match('/(android)|(iphone)|(windows)|(ipod)/i', $userAgent, $matched);
        if (!empty($matched)) {
            return $matched[0];
        }else{
            return '';
        }
    }

    protected static function recordDataToDB($level, $header, $data, $logUser){

    }

    protected static function recordDataToFile($level, $header, $data, $logUser){
        $file = self::$filePath . '/' . date('md-Y', time()) . '-' . $logUser . '.txt';
        $sufix = "\n\n";

        ob_start();
        var_dump($data);
        $s = ob_get_contents();
        ob_end_clean();

        $fp = fopen($file, 'ab');
        if(false == $fp){
            throw new \Exception('Debug Error: Can not open '.$file.'.');
        }

        fwrite($fp, $header.$s.$sufix);
        fclose($fp);
    }

    protected static function initRecord(){

    }

    protected static function closeRecord(){

    }
}
