<?php


namespace service\weixin;


use service\base\Base;
use service\weixin\models\WeixinMsg as WeixinMsg;

class Weixin extends \service\base\Module
{
    //公众号appid，如果是企业号则对应的是CorpID
    public $appid;
    //公众号appsecret，如果是企业号则对应的是Secret
    public $appsecret;
    //checksignature token
    public $token;
    //AES密钥
    public $encodingaeskey;

    //微信订阅号
    const TYPE_SUBCRIBE = 'subscribe';
    //微信服务号
    const TYPE_OFFICIAL = 'official ';
    //微信企业号
    const TYPE_ENTERPRISE = 'enterprise';

    //标记当前操作的所针对的公众号类型，如要调用企业号接口，需先将该属性标记为enterprise，再调用相应loadXXX()方法
    public $service_type = self::TYPE_OFFICIAL;

    public function init()
    {
        parent::init();
    }

    /**
     * 加载服务
     * @param $service
     * @return object
     */
    public function loadService($service)
    {

        if (empty($this->appid) || empty($this->appsecret))
            return $this->buildResponse('error', 400, 'appid or appsecret must not be null');

        $ns = '\\service\\weixin%s\\' . ucfirst($service);
        $path = '\\official';
        if ($this->service_type == self::TYPE_ENTERPRISE) $path = '\\enterprise';
        $ns = sprintf($ns, $path);
        return Base::loadService($ns, [], $this);
    }

    /**
     * 获取操作微信access的服务
     * @return \service\weixin\enterprise\Access||\serive\weixin\Access
     */
    public function loadAccess()
    {
        return $this->loadService('access');
    }

    /**
     * 获取信息发送消息的服务
     * @return \service\weixin\enterprise\Message||\serive\weixin\Message
     */
    public function loadMessage()
    {
        return $this->loadService('message');
    }

    /**
     * 获取操作微信菜单的服务
     * @return object
     */
    public function loadMenu()
    {
        return $this->loadService('menu');
    }
    /**
     * 获取微信素材服务
     * @return \service\weixin\enterprise\Message||\serive\weixin\Message
     */
    public function loadMaterial()
    {
        return $this->loadService('material');
    }
    /**
     * 加载成员管理service
     * @return object
     */
    public function loadUser()
    {
        return $this->loadService('user');
    }

    /**
     * 加载素材服务
     * @return object
     */
    public function loadMedia($token)
    {

        //TODO 素材上传测试

        $api = 'https://qyapi.weixin.qq.com/cgi-bin/media/upload?access_token=ACCESS_TOKEN&type=TYPE';

        $api = str_replace(['ACCESS_TOKEN', 'TYPE'], [$token, 'image'], $api);
        echo '<br><br>' . $api . '<br><br>';

        $filepath = 'd:\\it.png';

        if (class_exists('\CURLFile')) {
            $field = array('fieldname' => new \CURLFile(realpath($filepath)));
        } else {
            $field = array('fieldname' => '@' . realpath($filepath));
        }

        $ch = curl_init($api);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $field);

        $res = curl_exec($ch);

        echo "[" . curl_errno($ch) . "]";
        if (curl_errno($ch)) {
            var_dump(curl_error($ch));
            exit();
        }

        var_dump($res);


    }

    /**
     * 记录微信传输的XML入库(微调研使用中)
     * @param string $msg
     * @param string $event
     * @param string $extra 附加信息，如msg_signature，timestamp，nonce
     * @param string $type
     * @return bool
     */
    public function addMsg($type = "",$event = "",$extra = "",$userName='',$msg = "")
    {
        if(!$msg){
            return false;
        };
        $model = new WeixinMsg();
        $model->msg_type = $type;
        $model->msg_event = $event;
        $model->extra_json = $extra;
        $model->user_name = $userName;
        $model->msg_json = $msg;
        $model->add_time = date("Y-m-d H:i:s");
        return $model->save();
    }


    /**
     * 根据条件获取记录
     * @param array $cond
     * @return array|bool|null|\yii\db\ActiveRecord
     */
    public function getMsgRecordNumByCond($cond = array()){
        if(!$cond){
            return false;
        }
        $num = WeixinMsg::find()->where($cond)->count();
        if($num){
            return $num;
        }else{
            return false;
        }
    }
}
