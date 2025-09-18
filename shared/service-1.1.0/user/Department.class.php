<?php
namespace service\user;

/*
 * Service:user\Role
 * 
 */

use \service\base\ApiClient as ApiClient;

class Department extends \service\base\Module
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
    public function load($dept_id)
    {
        if (!is_numeric($dept_id)) {
            return $this->buildResponse('error', 401, 'Invalid role_id');
        }

        return self::$uc_client->send('/departments/' . $dept_id, []);
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
    public function create($department_data, $user_code){
        $post_data = array_merge($department_data, ['operator_code'=>$user_code, 'status'=>'active']);
        return self::$uc_client->send('/department', $post_data, 'POST');
    }
    
    
    public function update($department_id, $department_data, $user_code){
        $post_data = array_merge($department_data, ['operator_code'=>$user_code]);
        return self::$uc_client->send('/department/' . $department_id . '/update', $post_data, 'POST');
    }
    
    
    public function loadChildrenDepts($corp_id, $dept_id){
        $api_url = sprintf('/department/%s/children-depts', $dept_id);
        
        $post_data = [
            'corp_id' => $corp_id
        ];
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }
    
    
    public function loadPositions($corp_id, $dept_id){
        $api_url = sprintf('/department/%s/positions', $dept_id);
        
        $post_data = [
            'corp_id' => $corp_id
        ];
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }
    
    
    public function loadMembers($corp_id, $dept_id){
        $api_url = sprintf('/department/%s/members', $dept_id);
        
        $post_data = [
            'corp_id' => $corp_id
        ];
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }
    
    
    /**
     * Create position in department
     * 
     * @param int  $corp_id Corporation ID
     * @param type $dept_id Department ID
     * @param type $position_data
     * @param type $operator_code The user_code of operator
     * 
     * @return entity Created position entity data
     */
    public function createPosition($corp_id, $dept_id, $position_data, $operator_code){
        $api_url = sprintf('/department/%s/create-position', $dept_id);
        
        if (!is_string($operator_code) || 64!==strlen($operator_code)) {
            return $this->buildResponse('error', 400, 'Invalid operator code.');
        }
        
        $position_data['corp_id'] = $corp_id;
        return self::$uc_client->send($api_url, $position_data, 'POST');
    }
    
    
    /**
     * Load position information
     * 
     * @param int $corp_id Corporation ID
     * @param int $dept_id Department ID
     * @param int $po_id Position ID
     * 
     * @return Position entity information.
     */
    public function loadPosition($corp_id, $dept_id, $po_id, $operator_code){
        $api_url = sprintf('/departments/%s/load-position', $dept_id);
        
        if (!is_string($operator_code) || 64!==strlen($operator_code)) {
            return $this->buildResponse('error', 400, 'Invalid operator code.');
        }
        $post_data = [
            'corp_id' => $corp_id,
            'position_id' => $po_id,
            'operator_code' => $operator_code
        ];
        return self::$uc_client->send($api_url, $post_data, 'GET');
    }
    
    
    /**
     * Update position information in department
     * 
     * @param int  $dept_id
     * @param int  $position_data
     * @param int  $operator_code
     */
    public function updatePosition($corp_id, $dept_id, $position_data, $operator_code){
        $api_url = sprintf('/department/%s/update-position', $dept_id);
        
        if (!is_string($operator_code) || 64!==strlen($operator_code)) {
            return $this->buildResponse('error', 400, 'Invalid operator code.');
        }
        
        $position_data['corp_id'] = $corp_id;
        return self::$uc_client->send($api_url, $position_data, 'POST');
    }
    
    
    /**
     * Update position information in department
     * 
     * @param int    $dept_id
     * @param array  $position_data
     * @param string $operator_code
     */
    public function removePosition($corp_id, $dept_id, $po_id, $operator_code){
        $api_url = sprintf('/department/%s/remove-position', $dept_id);
        
        if (!is_string($operator_code) || 64!==strlen($operator_code)) {
            return $this->buildResponse('error', 400, 'Invalid operator code.');
        }
        
        $post_data = [
            'corp_id' => $corp_id,
            'position_id' => $po_id,
            'department_id' => $dept_id,
            'operator_code' => $operator_code
        ];
        
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }
    
    
    /**
     * Add employee to department
     * 
     * @param int $dept_id  Department ID
     * @param int $em_id  Employee ID
     * @param int $po_id  Position ID
     * @param int $operator_code  Operator code
     * 
     * @return status information
     */
    public function addEmployee($corp_id, $dept_id, $po_id, $employee_id, $status, $operator_code){
        $api_url = sprintf('/department/%s/add-employee', $dept_id);
        
        if (!is_string($operator_code) || 64!==strlen($operator_code)) {
            return $this->buildResponse('error', 400, 'Invalid operator code.');
        }
        
        $post_data = [
            'corp_id' => $corp_id,
            'position_id' => $po_id,
            'department_id' => $dept_id,
            'employee_id' => $employee_id,
            'operator_code' => $operator_code,
            'status' => $status
        ];
        
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }
    
    
    /**
     * Load employee entity with position information from department
     * 
     * @param int $dept_id Department ID
     * @param int $em_id Employee ID
     * 
     * @return json employee entity information in department.
     */
    public function loadEmployee($corp_id, $dept_id, $employee_id, $operator_code){
        $api_url = sprintf('/department/%s/load-employee', $dept_id);
        
        $post_data = array(
            'corp_id' => $corp_id,
            'department_id' => $dept_id,
            'employee_id' => $employee_id
        );
        
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }
    
    
    /**
     * Update employee position in department
     * 
     * @param type $dept_id
     * @param type $em_id
     * @param type $po_id
     */
    public function updateEmployee($corp_id, $dept_id, $po_id, $employee_id, $status, $operator_code){
        $api_url = sprintf('/department/%s/update-employee', $dept_id);
        
        if (!is_string($operator_code) || 64!==strlen($operator_code)) {
            return $this->buildResponse('error', 400, 'Invalid operator code.');
        }
        
        $post_data = [
            'corp_id' => $corp_id,
            'position_id' => $po_id,
            'department_id' => $dept_id,
            'employee_id' => $employee_id,
            'operator_code' => $operator_code,
            'status' => $status
        ];
        
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }
    
    
    /**
     * Lock or remove employee from department
     * @param type $dept_id
     * @param type $em_id
     * @param type $status
     */
    public function removeEmployee($corp_id, $dept_id, $po_id, $employee_id, $operator_code){
        $api_url = sprintf('/department/%s/remove-employee', $dept_id);
        
        if (!is_string($operator_code) || 64!==strlen($operator_code)) {
            return $this->buildResponse('error', 400, 'Invalid operator code.');
        }
        
        $post_data = [
            'corp_id' => $corp_id,
            'position_id' => $po_id,
            'department_id' => $dept_id,
            'employee_id' => $employee_id,
            'operator_code' => $operator_code,
            'status' => 'remove'
        ];
        
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }
}