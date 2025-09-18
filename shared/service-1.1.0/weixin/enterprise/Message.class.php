<?php
namespace service\weixin\enterprise;

use service\base\Curl;
use yii\web\View;

class Message extends \service\base\Module
{

    protected static $api_url = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=ACCESS_TOKEN';
    protected $types = [];


    public function init()
    {
        $this->types = ['text', 'news', 'image', 'file'];
    }


    /**
     * Build message xml content
     *
     * @param unknown $type
     * @param unknown $params parameters for building message body.
     */
    public function build($type, $params = [])
    {

        /*
        $tpl_file = sprintf('%s/mes_tpls/%s.tpl.php', __DIR__, $type);
        
        if (!file_exists($tpl_file)) {
            \GtoolsDebug::testLog(__FILE__, $tpl_file, __METHOD__);
            return $this->buildResponse('failed', 501, Yii::t('com/weixin', 'Template file does not exist.'));
        }
        */

        $process_handler = sprintf('build%s', ucfirst($type));

        if (method_exists($this, $process_handler)) {
            return $this->$process_handler($params);
        }

        return $this->buildResponse('failed', 502, Yii::t('com/weixin', 'Message type does not supported.'));
    }


    /**
     *
     * 向推送微信消息
     * @param string $target , user|tag|party
     * @param $id 推送目标的id 若target为user，则id可为 userid1|userid2|userid3， target为tag或party时，id类同
     * @param int $agent_id 企业号应用的id
     * @param $type 消息的类型 可用的值:text, image, voice, video, file, news, mpnews
     * @param array $mes 消息体
     * @see: http://qydev.weixin.qq.com/wiki/index.php?title=%E6%B6%88%E6%81%AF%E7%B1%BB%E5%9E%8B%E5%8F%8A%E6%95%B0%E6%8D%AE%E6%A0%BC%E5%BC%8F#file.E6.B6.88.E6.81.AF
     * @return mixed|\service\base\type
     */
    public function push($target, $id, $agent_id, $type, $mes)
    {
        
        if (!is_array($mes) || !array_key_exists('msgtype', $mes) || !in_array($type, ['text', 'image', 'voice', 'video', 'file', 'news', 'mpnews'])) {
            return $this->buildResponse('error', 400, 'Message type should be set, and is one of "text|image|voice|video|file|news|mpnews"');
        }

        $t_args = ['user' => 'touser', 'department' => 'toparty', 'tag' => 'totag'];
        if (!array_key_exists($target, $t_args)) {
            return $this->buildResponse('error', 400, 'Target only be one of "user|department|tag"');
        }

        $mes_args = array();
        $target_name = $t_args[$target];
        $mes_args[$target_name] = $id;
        $mes_args['agentid'] = $agent_id;

        $mes_args = array_merge($mes_args, $mes);

//        \GtoolsDebug::testLog(__FILE__, $mes_args, __METHOD__);

        return $this->_push($mes_args);
    }


    public function sendNotice($mes_str)
    {
        $access = $this->module->loadAccess();
        $access_token = $access->access();

        $com_curl = new Curl();
        $url = str_replace('ACCESS_TOKEN', $access_token, self::$api_url);

        $re = $com_curl->post($url, $mes_str);
        \GtoolsDebug::testLog(__FILE__, [$this->module, $access_token, $url, $re], __METHOD__);
        if (isset($re['code']) && $re['code'] == 500) {
            return $this->buildResponse('failed', 500, $re['data']);
        }

        if (isset($re['errcode']) && $re['errcode'] != 0) {
            return $this->buildResponse('failed', '500', $re['errmsg']);
        }

        return true;
    }


    protected function _push($mes_content)
    {
        $access = $this->module->loadAccess();
        $access_token = $access->access();

        $com_curl = new Curl();

        $url = str_replace('ACCESS_TOKEN', $access_token, self::$api_url);
        //\GtoolsDebug::testLog(__FILE__, $url, __METHOD__);
        $mes_str = json_encode($mes_content, JSON_UNESCAPED_UNICODE);

        //TODO 加密处理
//        require_once(DIR_APP_ROOT . '/components/weixin/WXBizMsgCrypt/WXBizMsgCrypt.php');
//        $current_corp = $this->module->appid;
//
//        $wx_crypt = new \WXBizMsgCrypt(
//            $this->module->token,
//            $this->module->encodingaeskey,
//            $current_corp
//        );
//
//        $is_encrypted = $wx_crypt->EncryptMsg($mes_str, time(), md5('DailyEdu.com'), $encrypted_mes);

        $re = $com_curl->post($url, $mes_str);

//        var_dump($re);

        if (isset($re['code']) && $re['code'] == 500)
            return $this->buildResponse('failed', 500, $re['data']);

        if (isset($re['errcode']) && $re['errcode'] != 0) {
            return $this->buildResponse('failed', '500', $re['errmsg']);
        }

        return true;
    }


    /**
     * Build and push file to weixin enterprise material repositry.
     * @param unknown $params
     *     - file_path is required
     */
    public function buildFile($params)
    {
        $wx_material = new \com\weixin\enterprise\Material();

        $file_path = $params['file_path'];

        \GtoolsDebug::testLog(__FILE__, [$params, $file_path], __METHOD__);
        if (!file_exists($file_path)) {
            return $this->buildResponse('error', 400, sprintf('File "%s" does not exists.', $file_path));
        }

        $re = $wx_material->push($params, $file_path);

        if (false === $re || !array_key_exists('media_id', $re)) {
            return $this->buildResponse('failed', 500, $re);
        }
        \GtoolsDebug::testLog(__FILE__, $params, __METHOD__);
        //TODO: record push information, If the file pushed and It is still valid. don't push it again.
        $type = $params['type'];
        $mes_args = [
            'msgtype' => $type,
            $type => ['media_id' => $re['media_id']]
        ];

        \GtoolsDebug::testLog(__FILE__, $re, __METHOD__);

        return $this->buildResponse('success', 201, $mes_args);
    }


    public function buildCustomMessage($type, $params, $receiver)
    {

        return $this->buildTextCustomMessage($params, $receiver);
    }


    public function buildTextCustomMessage($params, $receiver)
    {

        return json_encode([
            'touser' => $receiver['FromUserName'],
            'msgtype' => 'text',
            'text' => [
                'content' => $params['content']
            ]
        ], JSON_UNESCAPED_UNICODE);
    }


    public function processImage()
    {

    }


    public function buildText($receiver, $params = [])
    {

        if (!array_key_exists('content', $params)) {
            //TODO Yii::warning('weixin/message', 'Failed to build text');
            return null;
        }
        \GtoolsDebug::testLog(__FILE__, $receiver, __METHOD__);
        return $this->render('text', [
            'receiver' => $receiver,
            'content' => $params['content']
        ]);
    }


    public function processNews($receiver, $params = [])
    {

    }


    public function render($tpl_name, $vars)
    {
        $com_yii_view = new View();
        $ns_view = sprintf('@app/components/weixin/enterprise/mes_tpls/%s', $tpl_name);

        return $com_yii_view->render($ns_view, $vars);
    }
}