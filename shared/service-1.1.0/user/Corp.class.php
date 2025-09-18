<?php
namespace service\user;

/*
 * Service:user\Role
 * 
 */

use \service\base\ApiClient as ApiClient;

class Corp extends \service\base\Module
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
    public function load($corp_id)
    {
        if (!is_numeric($corp_id)) {
            return $this->buildResponse('error', 401, 'Invalid role_id');
        }

        return self::$uc_client->send('/corps/' . $corp_id, []);
    }
    
    
    /**
     * Generally when you create an new corporation, the corporation is inactive.
     * You should active the corporation manually.
     * 
     * Options field for $corp_data:
     * - society_credit_code [required] 企业统一社会信用编码
     * - name [required] 企业名称
     * - owner_id 企业所有人ID，如果企业所有人已在系统中注册，可关联企业所有人ID
     * - parent_id 父级企业ID
     * - business_license_number 工商营业执照注册号，只针对旧版营业执照
     * - organization_code 企业组织机构代码证号码， 只针对旧版营业执照
     * - legal_representative_name 法人代表名称
     * - legal_representative_id_card 法人代表身份证号码
     * - business_register_date 企业注册时间
     * - last_editor 企业信息最后编辑人
     * - description 企业介绍
     * 
     * @return type
     */
    public function create($corp_data, $user_code){
        $post_data = array_merge($corp_data, ['operator_code'=>$user_code, 'status'=>'inactive']);
        return self::$uc_client->send('/corp', $post_data, 'POST');
    }
    
    
    /**
     * Active corporation
     * 
     * 
     * @param type $corp_id
     * @param type $user_code
     */
    public function active($corp_id, $user_code){
        $post_data =  ['operator_code'=>$user_code, 'status'=>'active'];
        return self::$uc_client->send('/department/' . $corp_id , $post_data, 'POST');
    }
    
    
    public function update($corp_id, $corporation_data, $user_code){
        $post_data = array_merge($corporation_data, ['operator_code'=>$user_code]);
        return self::$uc_client->send('/corporation/' . $corp_id . '/update', $post_data, 'POST');
    }
    
    
    /**
     * 
     * @param type $society_credit_code
     */
    protected function buildCorpCode($society_credit_code){
        return hash('sha256', $society_credit_code);
    }
}