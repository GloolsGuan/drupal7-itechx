<?php


namespace service\user;


use service\user\models\Mapping;

class Weixin extends \service\base\Module
{

    /**
     * 为用户和企业号中的账号建立关联
     * @param $unique_code
     * @param $userid
     * @return bool
     */
    public function createMap($unique_code, $userid)
    {
        return Mapping::create($unique_code, $userid);
    }

    /**
     * 解除用户和企业号中账号的关联关系
     * 取消unique_code和企业号的关联
     * @param $unique_code
     * @return bool|false|int
     */
    public function removeMap($unique_code)
    {
        return Mapping::remove($unique_code);
    }

    /**
     * 获取用户关联的企业号userid
     * @param $unique_code
     * @return bool|mixed
     */
    public function getUserId($unique_code)
    {
        return Mapping::getUserid($unique_code);
    }

    /**
     * @return \service\base\type
     */
    public function buildUniqueCode($username)
    {
        $unique_code = Base::buildUniqueCode($username);

        if (false === $unique_code) {
            return $this->buildResponse('error', 400, sprintf('Invalid login name "%s".', $login_name));
        }
    }

    /**
     * 查询user_id所关联的ucenter中的用户
     * @param $user_id 企业号中的user_id
     * @return mixed|string
     */
    public function getUniqueCode($user_id){
        return Mapping::getUniqueCode($user_id);
    }

}