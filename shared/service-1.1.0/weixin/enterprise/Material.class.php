<?php
namespace service\weixin\enterprise;

use service\base\Base;

use service\weixin\Curl;

class Material extends \service\base\Module
{

    private $api_url = 'https://qyapi.weixin.qq.com/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type=TYPE';

    private $api_url_stable = 'https://qyapi.weixin.qq.com/cgi-bin/material/add_material?agentid=AGENTID&type=TYPE&access_token=ACCESS_TOKEN';

    protected $curl;

    public function init()
    {
        parent::init();
        $this->curl = new Curl();
    }

    /**
     *
     *上传素材
     * @return object
     * @param string $token
     * @param string $filepath 文件的绝对路径
     * @return mixed|\service\base\type
     */
    public function uploadMaterial($token, $filepath, $type)
    {
        $allowed_type = ['image', 'voice', 'video', 'file'];

        if (!in_array($type, $allowed_type)) {
            return $this->buildResponse('failed', '400', '不支持此文件类型');
        }

        $url = str_replace(['ACCESS_TOKEN', 'TYPE'], [$token, $type], $this->api_url);

        $filepath = realpath($filepath);
        \GtoolsDebug::testLog(__METHOD__,$filepath,__FILE__.__LINE__);

        if (!file_exists($filepath) || filesize($filepath) == 0)
            return $this->buildResponse('error', '400', '文件不存在');

        if (class_exists('\CURLFile')) {
            $field = array('media' => new \CURLFile($filepath));
        } else {
            $field = array('media' => '@' . $filepath);
        }


        $res = $this->curl->postFile($url, $field);
//        \GtoolsDebug::testLog(__FILE__, $res, __METHOD__);

        if (!isset($res['data']))
            return $this->buildResponse('failed', '400', '文件错误');

//return $res;
        return $this->buildResponse('success', '200', json_decode($res['data'], true)['media_id']);


    }

    public function push($params, $file_path)
    {

        if (!file_exists($file_path)) {
            return $this->buildResponse('erroe', 400, 'Invalid file path, The file does not exists..');
        }

        // Checking the file type, If it is HTML5, Just redirect to HTML5 page.

        //TODO Checking whether the file has been pushed.
        // If yes, Loading pushing file information from database, and return media_id parameter.
        // Checing md5 value of file.


        $allowed_type = ['image', 'voice', 'video', 'file'];

        if (!array_key_exists('type', $params) || !in_array($params['type'], $allowed_type)) {
            return 401;
        }

        $path_info = pathinfo($file_path);
        $new_file_path = sprintf('/tmp/%s.%s', $params['title'], $params['ext']);
        copy($file_path, $new_file_path);
        \GtoolsDebug::testLog(__FILE__, [$params, $file_path, $new_file_path, basename($new_file_path)], __METHOD__ . ' ' . __LINE__);

        //$url = $this->buildUrlForTemporary($params['type']);
        $url = $this->buildUrlForStableMaterial($params['type']);
        $command = sprintf('curl -F media=@%s "%s"', $new_file_path, $url);
        $output = '';
        exec($command, $output, $return);

        \GtoolsDebug::testLog(__FILE__, [$command, $output, $return], __METHOD__);

        if (0 == $return) {
            //TODO Saving pushing information.
            return json_decode($output[0], true);
        }

        return false;
    }


    protected function buildUrlForTemporary($type)
    {
        $access_token = $this->getAccessToken();

        //-- Short time material --
        return str_replace(['ACCESS_TOKEN', 'TYPE'], [$access_token, $type], self::$api_url);
    }


    /**
     * Stable material (agentid=AGENTID&type=TYPE&access_token=ACCESS_TOKEN)
     */
    protected function buildUrlForStableMaterial($type)
    {
        $access_token = $this->getAccessToken();
        $current_corp = $this->loadParams('weixin/current_corp');
        $agent_id = $this->loadParams(sprintf('weixin/corps/%s/app_id', $current_corp));

        return str_replace(['AGENTID', 'ACCESS_TOKEN', 'TYPE'], [$agent_id, $access_token, $type], self::$api_url_stable);
    }


    protected function getAccessToken()
    {
        $wx_access = new \com\weixin\enterprise\Access();
        return $wx_access->access();
    }


    /**
     * https://segmentfault.com/a/1190000005631406
     * @param unknown $file_path
     */
    protected function getFileContent($file_path)
    {

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $params = array(
            'filename' => basename($file_path),
            'content-type' => finfo_file($finfo, $file_path),
            'filelength' => filesize($file_path)
        );

        return $params;
    }
}