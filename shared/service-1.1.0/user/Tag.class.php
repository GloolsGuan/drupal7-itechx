<?php
namespace service\user;

use service\user\interfaces\Tag as TagInterface;
use \service\base\ApiClient as ApiClient;

class Tag extends \service\base\Module implements TagInterface
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
    public function load($tag_name)
    {
        if (!is_string($tag_name) || strlen($tag_name) > 20) {
            return $this->buildResponse('error', 401, 'Invalid tag name.');
        }

        return self::$uc_client->send('/tags/' . $tag_name, []);
    }


    /**
     * Load children tags
     *
     * @param string $tag_name
     * @return array or error message.
     */
    public function loadChildren($tag_name)
    {
        if (!is_string($tag_name) || strlen($tag_name) > 20) {
            return $this->buildResponse('error', 401, 'Invalid tag name.');
        }

        $api_url = sprintf('/tag/%s/load-children', $tag_name);
        return self::$uc_client->send($api_url, [], 'POST');
    }


    public function create($tag_data, $operator_code)
    {
        $post_data = array_merge($tag_data, ['operator_code' => $operator_code]);
        $api_url = '/tag';
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }

    public function update($tag_id, $tag_data, $operator_code)
    {
        $post_data = array_merge($tag_data, ['operator_code' => $operator_code]);
        $api_url = sprintf('/tag/%s/update', $tag_id);
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }

    public function remove($tag_id, $operator_code)
    {
        $post_data = ['tag_id' => $tag_id, 'operator_code' => $operator_code];
        $api_url = sprintf('/tag/%s/remove', $tag_id);
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }


    /**
     * Load members by tag ID
     * @param type $tag_id
     */
    public function loadMemberById($tag_id)
    {

    }

    public function loadMembersByTagName($tag_name)
    {

        $load_result = $this->load($tag_name);

        if ('success' == $load_result['status'] && 201 == $load_result['code']) {
            $tag_entity_data = $load_result['data'];
        } else {
            return $this->buildResponse('error', 400, 'Tag name is not exists.');
        }

        return $this->loadMembersById($tag_id);
    }


    public function loadMembers($tag_id)
    {
        $post_data = [
            'tag_id' => $tag_id
        ];
        $api_url = sprintf('/tag/%s/members', $tag_id);
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }


    public function addMembers($tag_id, $members, $operator_code)
    {
        if (!is_numeric($tag_id)) {
            return $this->buildResponse('error', 400, 'Invalid tag_id.');
        }

        $post_data = ['members' => $members, 'tag_id' => $tag_id, 'operator_code' => $operator_code];
        $api_url = sprintf('/tag/%s/add-members', $tag_id);
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }


    public function removeMember($tag_id, $member_code, $operator_code)
    {
        $post_data = ['member_code' => $member_code, 'operator_code' => $operator_code];
        $api_url = sprintf('/tag/%s/remove-member', $tag_id);
        return self::$uc_client->send($api_url, $post_data, 'POST');
    }

}