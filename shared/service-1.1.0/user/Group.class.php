<?php
namespace service\user;

/*
 * Service:user\Role
 * 
 */
use service\user\interfaces\Group as GroupInterface;
use \service\base\ApiClient as ApiClient;

class Group extends \service\base\Module
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
     * Return role entity data
     * @param type $role_code
     */
    public function load($group_id)
    {
        if (!is_numeric($group_id)) {
            return $this->buildResponse('error', 401, 'Invalid role_id');
        }

        return self::$uc_client->send('/groups/' . $group_id, []);
    }

    public function create($group_data, $user_code)
    {
        $post_data = array_merge($group_data, ['operator_code' => $user_code, 'status' => 'active']);
        return self::$uc_client->send('/department', $post_data, 'POST');
    }


    public function update($group_id, $group_data, $user_code)
    {
        $post_data = array_merge($group_data, ['operator_code' => $user_code]);
        return self::$uc_client->send('/group/' . $group_id . '/update', $post_data, 'POST');
    }
}