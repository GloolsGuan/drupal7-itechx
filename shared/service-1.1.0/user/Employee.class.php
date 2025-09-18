<?php
namespace service\user;

/*
 * Service:user\Role
 * 
 */

use \service\base\ApiClient as ApiClient;

class Employee extends \service\base\Module
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
    public function load($em_id)
    {
        if (!is_numeric($em_id)) {
            return $this->buildResponse('error', 401, 'Invalid role_id');
        }

        return self::$uc_client->send('/employee/' . $em_id, []);
    }
    
    
    /**
     * Create department
     * 
     * Parameters of $department_data:
     * - corp_id [required]  corporations.id
     * - title [required] department title 
     * - intro  department introduction
     * - logo Department logo url
     * - director_id the director should be the in same corporation
     * - parent_id Parent department id
     * - status Default 'active', The value of status should be one of "inactive", "active", "locked", "expired", "removed"
     * 
     * @param type $department_data
     * @param type $user_code
     * @return type
     */
    public function create($employee_data, $user_code){
        $post_data = array_merge($employee_data, ['operator_code'=>$user_code]);
        return self::$uc_client->send('/employee', $post_data, 'POST');
    }
    
    
    public function update($em_id, $employee_data, $user_code){
        $post_data = array_merge($employee_data, ['operator_code'=>$user_code]);
        return self::$uc_client->send('/employee/' . $em_id . '/update', $post_data, 'POST');
    }
    
    public function remove($corp_id, $employee_id,$operator_code){
        
    }
    
}