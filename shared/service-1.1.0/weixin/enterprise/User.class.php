<?php


namespace service\weixin\enterprise;

use service\weixin\Curl;

/**
 * Class User
 * @package service\weixin\enterprise
 * @see http://qydev.weixin.qq.com/wiki/index.php?title=%E7%AE%A1%E7%90%86%E6%88%90%E5%91%98#.E8.8E.B7.E5.8F.96.E6.88.90.E5.91.98
 */
class User extends \service\base\Module
{
    private $api_user_info = 'https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=ACCESS_TOKEN&userid=USERID';
    private $api_user_info_detail = 'https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=ACCESS_TOKEN&department_id=DEPARTMENT_ID&fetch_child=FETCH_CHILD&status=STATUS';
    protected $api_get_contacts = 'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token=ACCESS_TOKEN&department_id=DEPARTMENT_ID&fetch_child=FETCH_CHILD&status=STATUS';

    private $api_user_create = 'https://qyapi.weixin.qq.com/cgi-bin/user/create?access_token=ACCESS_TOKEN';
    private $api_user_update = 'https://qyapi.weixin.qq.com/cgi-bin/user/update?access_token=ACCESS_TOKEN';
    private $api_user_delete = 'https://qyapi.weixin.qq.com/cgi-bin/user/delete?access_token=ACCESS_TOKEN&userid=USERID';
    private $api_convert_to_openid = 'https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=ACCESS_TOKEN';
    private $api_groups = 'https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=ACCESS_TOKEN&id=ID';
    private $api_tags = 'https://qyapi.weixin.qq.com/cgi-bin/tag/list?access_token=ACCESS_TOKEN';
    private $api_add_to_tag = 'https://qyapi.weixin.qq.com/cgi-bin/tag/addtagusers?access_token=ACCESS_TOKEN';
    /**
     * @var \service\weixin\enterprise\Access
     */
    protected $acces;

    /**
     * @var \service\base\Curl
     */
    protected $curl;

    public function init()
    {
        parent::init();
        $this->acces = $this->module->loadAccess();
        $this->curl = new Curl();
    }

    /**
     * laughstrm
     * 创建用户信息
     */
    public function createUser($data)
    {
        $com_access = $this->module->loadAccess();
        $url = str_replace(['ACCESS_TOKEN'], [$com_access->access()], $this->api_user_create);
        $res = $this->curl->post($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        //\GtoolsDebug::testLog(__METHOD__, $res, __FILE__ . __LINE__);
        $content = $res['data'];
        $data = json_decode($content, true);

        return $data;
    }
    
    public function addToTag($tag_id, $userlist){
        if (!is_numeric($tag_id)) {
            return false;
        }
        $com_access = $this->module->loadAccess();
        $url = str_replace(['ACCESS_TOKEN'], [$com_access->access()], $this->api_add_to_tag);
        $data = ['tagid'=>(string)$tag_id, 'userlist'=>$userlist, 'partylist'=>[]];
        
        $res = $this->curl->post($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        //\GtoolsDebug::testLog(__METHOD__, json_encode($data, JSON_UNESCAPED_UNICODE), __FILE__ . __LINE__);
        $content = $res['data'];
        $data = json_decode($content, true);

        return $data;
    }
    
    public function loadGroups(){
        $com_access = $this->module->loadAccess();
        $url = str_replace(['ACCESS_TOKEN', 'ID'], [$com_access->access(), 0], $this->api_groups);
        \GtoolsDebug::testLog(__METHOD__, [$com_access->access(), $url]);
        $res = $this->curl->get($url);
        $content = $res['data'];
        $data = json_decode($content, true);

        return $data;
    }
    
    
    public function loadTags(){
        $com_access = $this->module->loadAccess();
        $url = str_replace('ACCESS_TOKEN', $com_access->access(), $this->api_tags);
        
        $res = $this->curl->get($url);
        $content = $res['data'];
        $data = json_decode($content, true);

        return $data;
    }

    /**
     * laughstrm
     * 更新用户信息
     */
    public function updateUser($data)
    {
        $url = str_replace(['ACCESS_TOKEN'], [$this->acces->access()], $this->api_user_update);
        $res = $this->curl->post($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        $content = $res['data'];
        $data = json_decode($content, true);

        return $data;
    }

    /**
     * laughstrm
     * 删除用户信息
     */
    public function deleteUser($user_id)
    {
        $url = str_replace(['ACCESS_TOKEN', 'USERID'], [$this->acces->access(), $user_id], $this->api_user_delete);
        $res = $this->curl->get($url);

        $content = $res['data'];
        $data = json_decode($content, true);

        return $data;
    }

    /**
     * 获取用户信息
     * @param $user_id
     */
    public function getUserInfo($user_id)
    {
        $url = str_replace(['ACCESS_TOKEN', 'USERID'], [$this->acces->access(), $user_id], $this->api_user_info);

        $res = $this->curl->get($url);

        if (isset($content['code']) && 500 == $res['code'])
            return $this->buildResponse($res['status'], $res['code'], '');

        $content = $res['data'];

        $data = json_decode($content, true);

        if ($data && isset($data['errcode']) && $data['errcode'] == 0)
            return $data;

        return $this->buildResponse('error', $data['errcode'], $data['errmsg']);
    }

    /**
     * 获取用户详情
     */
    public function getUserInfoDetail($user_id)
    {
        $url = str_replace(['ACCESS_TOKEN', 'USERID'], [$this->acces->access(), $user_id], $this->api_user_info_detail);
    }


    /**
     * *** Short time method, The method should be finished. ***
     *
     * @param type $wx_department_id
     * @return type
     */
    public function getContacts($wx_department_id = 35)
    {
        $url = str_replace(['ACCESS_TOKEN', 'DEPARTMENT_ID', 'FETCH_CHILD', 'STATUS'], [$this->acces->access(), $wx_department_id, 1, 0], $this->api_get_contacts);
        $res = $this->curl->get($url);
        if (empty($res) || !array_key_exists('data', $res)) {
            return $this->buildResponse('failed', 500, 'Failed to connect to weixin server..');
        }

        $result = json_decode($res['data'], true);
        if (0 != $result['errcode']) {
            return $this->buildResponse('error', 501, $result['errmsg']);
        }

        $users = $result['userlist'];
        //\GtoolsDebug::testLog(__FILE__, $result, __METHOD__);
        if (empty($users)) {
            return $this->buildResponse('success', 299, 'There is no user in weixin contact list.');
        }

        $service_user = new \service\user\User();
        $service_user_wx = new \service\user\Weixin();
        //\GtoolsDebug::testLog(__FILE__, $service_user, __METHOD__);
        $errors = [];
        foreach ($users as $user) {
            $uc_result = $service_user->register(['login_name' => $user['userid'], 'safe_mobile' => $user['userid'], 'safe_email' => '', 'password' => $user['userid'], 'user_name' => $user['name']]);
            if (299 == $uc_result['code'] && array_key_exists('login_name', $uc_result['data'])) {
                $unique_code = $uc_result['data']['unique_code'];
                $wx_user_id = $user['userid'];
                $map = $service_user_wx->createMap($unique_code, $wx_user_id);
                \GtoolsDebug::testLog(__FILE__, $map, __METHOD__);
            } else {
                $errors[$user['name']]['uc'] = $uc_result;
            }
        }

        //\GtoolsDebug::testLog(__FILE__, [$uc_result, $errors], __METHOD__);
        return $users;
    }

    /**
     * @param string $userId 企业号userid
     * @param int $agentId 企业号 agentid 整型，需要发送红包的应用ID，若只是使用微信支付和企业转账，则无需该参数
     * @return mixed|\service\base\type
     */
    public function getOpenId($userId, $agentId = '')
    {
        $url = str_replace(['ACCESS_TOKEN'], [$this->acces->access()], $this->api_convert_to_openid);

        $data = ['userid' => $userId];

        if (!empty($agentId)) $data['agentid'] = $agentId;

        $res = $this->curl->post($url, json_encode($data, JSON_UNESCAPED_UNICODE));

        if (isset($content['code']) && 500 == $res['code'])
            return $this->buildResponse($res['status'], $res['code'], '');

        $content = $res['data'];

        $data = json_decode($content, true);

        if ($data && isset($data['errcode']) && $data['errcode'] == 0)
            return $data;

        return $this->buildResponse('error', $data['errcode'], $data['errmsg']);
    }

    /**
     * 获取部门下所有成员列表
     * @param int $departmentId         获取的部门id
     * @param int $fetchChild           1/0：是否递归获取子部门下面的成员
     * @param int $status               0 获取全部成员，
     *                                  1 获取已关注成员列表，
     *                                  2 获取禁用成员列表，
     *                                  4 获取未关注成员列表。status可叠加，未填写则默认为0
     * @return array|\service\base\type
     */
    public function getMembersByDepartmentId($departmentId = 0,$fetchChild = 1,$status = 0){
        if(!$departmentId){
            return $this->buildResponse('error', 400, "部门ID获取失败，请重试！");
        }
        $url = str_replace(['ACCESS_TOKEN', 'DEPARTMENT_ID', 'FETCH_CHILD', 'STATUS'], [$this->acces->access(), $departmentId, 1, 0], $this->api_get_contacts);
        $res = $this->curl->get($url);
        $res = json_decode($res['data'],true);
        if($res['errcode'] != 0){
            return $this->buildResponse('error', 400, "获取成员列表失败，请重试");
        };
        if(!$res['userlist']){
            return $this->buildResponse('success', 200, "用户列表为空");
        };
        return $this->buildResponse('success', 201, $res['userlist']);
    }

    /**
     * 获取部门下所有成员列表V2,成员为详细信息
     * @param int $departmentId         获取的部门id
     * @param int $fetchChild           1/0：是否递归获取子部门下面的成员
     * @param int $status               0 获取全部成员，
     *                                  1 获取已关注成员列表，
     *                                  2 获取禁用成员列表，
     *                                  4 获取未关注成员列表。status可叠加，未填写则默认为0
     * @return array|\service\base\type
     */
    public function getMembersByDepartmentIdV2($departmentId = 0,$fetchChild = 1,$status = 0){
        if(!$departmentId){
            return $this->buildResponse('error', 400, "部门ID获取失败，请重试！");
        }
        $url = str_replace(['ACCESS_TOKEN', 'DEPARTMENT_ID', 'FETCH_CHILD', 'STATUS'], [$this->acces->access(), $departmentId, 1, 0], $this->api_user_info_detail);
        $res = $this->curl->get($url);
        $res = json_decode($res['data'],true);
        if($res['errcode'] != 0){
            return $this->buildResponse('error', 400, "获取成员列表失败，请重试");
        };
        if(!$res['userlist']){
            return $this->buildResponse('success', 200, "用户列表为空");
        };
        return $this->buildResponse('success', 201, $res['userlist']);
    }
    

}