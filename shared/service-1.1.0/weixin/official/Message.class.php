<?php
namespace service\weixin\official;

use service\base\Module;

class Message extends Module
{

    protected $types = [];
    public $receiver = null;

    public function __construct($receiver)
    {

        parent::__construct(['receiver' => $receiver]);

        $this->types = ['text', 'news', 'image'];
    }


    public function build($type, $params, $receiver)
    {

        $tpl_file = sprintf('%s/mes_tpls/%s.php', __DIR__, $type);

        if (!file_exists($tpl_file)) {
            //\GtoolsDebug::testLog(__FILE__, $tpl_file, __METHOD__);
            return $this->buildResponse('failed', 501, Yii::t('com/weixin', 'Template file does not exist.'));
        }

        $process_handler = sprintf('build%s', ucfirst($type));

        if (method_exists($this, $process_handler)) {
            return $this->$process_handler($receiver, $params);
        }

        return $this->buildResponse('failed', 502, Yii::t('com/weixin', 'Message type does not supported.'));
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
        $com_yii_view = new YiiView();
        $ns_view = sprintf('@frontend/components/weixin/mes_tpls/%s', $tpl_name);

        return $com_yii_view->render($ns_view, $vars);
    }
}