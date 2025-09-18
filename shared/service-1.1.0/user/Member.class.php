<?php
namespace service\user;

/*
 * Service:user\Role
 * 
 */

use \service\base\ApiClient as ApiClient;

class Member extends \service\base\Module
{
    
    protected static $uc_server_conf = null;
    protected static $uc_client = null;
    
    
    public function init()
    {
        
        if (null == self::$conf) {
            self::$conf = include(dirname(__FILE__) . '/includes/params.conf.php');
        }

        self::$uc_server_conf = $this->loadParams('servers/ucenter', self::$conf, $this->_env);
        self::$uc_client = new ApiClient(self::$uc_server_conf);
    }
    
    /**
     * Load member entity data
     */
    public function load($user_code)
    {
        if (64!=strlen($user_code)) {
            return $this->buildResponse('error', 401, 'Invalid user_code');
        }

        return self::$uc_client->send('/members/' . $user_code, 'GET');
    }
    
    
    /**
     * Create member
     * 
     */
    public function create($member_data, $operator_code){
        $post_data = array_merge($member_data, ['operator_code'=>$operator_code]);
        return self::$uc_client->send('/member', $post_data, 'POST');
    }
    
    
    /**
     * Update member entity
     */
    public function update($user_code, $member_data, $operator_code){
        $post_data = array_merge($member_data, ['operator_code'=>$operator_code]);
        return self::$uc_client->send('/member/' . $user_code . '/update', $post_data, 'POST');
    }
    
    
    public function loadMembersByCode($codes)
    {
        if (!is_array($codes)) {
            return $this->buildResponse('error', 400, sprintf('Invalid query "%s".', $codes));
        }
        
        $members = self::$uc_client->send('/member/load-members', ['user_codes' => base64_encode(serialize($codes))], 'POST');
        //\GtoolsDebug::testLog(__FILE__, $members, __METHOD__);
        
        return $members;
    }
    
}