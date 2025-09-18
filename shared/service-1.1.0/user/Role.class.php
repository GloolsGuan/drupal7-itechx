<?php
namespace service\user;

/*
 * Service:user\Role
 * 
 */

use \service\base\ApiClient as ApiClient;

class Role extends \service\base\Module
{
    
    protected $version = 0.1;
    public $args=[];
    protected static $uc_client = null;
    protected static $authorization = null;
    protected static $conf = null;
    protected static $uc_server = null;


    public function init()
    {
        if (null == self::$conf) {
            self::$conf = include(dirname(__FILE__) . '/includes/params.conf.php');
        }

        self::$uc_server = $this->loadParams('servers/ucenter', self::$conf, $this->_env);
        self::$uc_client = new ApiClient(self::$uc_server);
    }
    
    
    
    /**
     * Return role entity data 
     * @param type $role_code
     */
    public function load($role_id)
    {
        if (!is_numeric($role_id)) {
            return $this->buildResponse('error', 401, 'Invalid role_id');
        }

        return self::$uc_client->send('/roles/' . $role_id, []);
    }
    
    
    /**
     * Create group of roles
     * 
     * @param string $title Role title
     * @param string $description Role description
     * 
     * @return array Role entity data
     */
    public function createGroup($title, $description=''){
        
        if(mb_strlen($title)>20) {
            return $this->buildResponse('error', 402, 'The max of role title length is 20 character or 10 chinese.');
        }
        
        if (mb_strlen($description)>100) {
            return $this->buildResponse('error', 403, 'The max of role description is 100 character or 50 chinese.');
        }
        
        $data = [
            'title' => $title,
            'desc' => $description
        ];

        return self::$uc_client->send('/role/create-group', $data, 'POST');
    }
    
    
    /**
     * Create role entity
     */
    public function createRole($title, $parent_id, $description=''){
         if(mb_strlen($title)>20) {
            return $this->buildResponse('error', 402, 'The max of role title length is 20 character or 10 chinese.');
        }
        
        if (mb_strlen($description)>100) {
            return $this->buildResponse('error', 403, 'The max of role description is 100 character or 50 chinese.');
        }
        
        $data = [
            'title' => $title,
            'parent_id' => $parent_id,
            'desc' => $description
        ];

        return self::$uc_client->send('/role', $data, 'POST');
    }
    
    
    public function updateRole($role_id, $title, $parent_id=null, $description=''){
         if(mb_strlen($title)>20) {
            return $this->buildResponse('error', 402, 'The max of role title length is 20 character or 10 chinese.');
        }
        
        if (mb_strlen($description)>100) {
            return $this->buildResponse('error', 403, 'The max of role description is 100 character or 50 chinese.');
        }
        
        if (!is_numeric($role_id)) {
            return $this->buildResponse('error', 404, 'Invalid role_id');
        }
        
        $data = [];
        if (!empty($title) && is_string($title)) {
            $data['title'] = $title;
        }
        
        if (!empty($parent_id) && is_numeric($parent_id)) {
            $data['parent_id'] = $parent_id;
        }
        
        if (!empty($description) && is_string($description)) {
            $data['desc'] = $description;
        }
        
        if (1>count($data)) {
            return $this->buildResponse('error', 405, 'There is no valid parameters.');
        }

        $url = sprintf('/role/%s/update', $role_id);
        return self::$uc_client->send($url, $data, 'POST');
    }
    
    
}

