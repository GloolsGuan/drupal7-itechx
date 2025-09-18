<?php
namespace service\user;

/*
 * Service:Account for all account of roles, including members and companys.
 *
 * The account service base on ucenter system.
 */

use service\base\Base;
use service\user\models\Mapping;
use \service\base\ApiClient as ApiClient;


class User extends \service\base\Module {
	protected $version = 0.1;
	public $args;
	protected static $uc_client = null;
	protected static $authorization = null;
	protected static $conf = null;
	protected static $uc_server = null;

	public function init() {
		if (null == self::$conf) {
			self::$conf = include dirname(__FILE__) . '/includes/params.conf.php';
		}

		self::$uc_server = self::$conf['uc_server'];
		//\GtoolsDebug::testLog(__FILE__, self::$uc_server, __METHOD__);
		self::$uc_client = new ApiClient(self::$conf['uc_server']);
	}

	/**
	 * Load user from ucenter server with registered roles and groups
	 *
	 * @param type $login_name
	 * @return type
	 */
	public function load($login_name) {

		if (!is_string($login_name) || strlen($login_name) > 64) {
			return $this->buildResponse('error', 400, sprintf('Invalid login name "%s".', $login_name));
		}

		$login_name = base64_encode($login_name);

		return self::$uc_client->send('/users/' . $login_name, ['access_token' => '7NHd0yZ8ItSR6Rsj__eQpm8j_eQ6GkPMHU2j9fEO']);
	}

	/**
	 *
	 * @param string $operator_login_name Who operate the role of the user
	 * @param string $user_login_name
	 * @param numeric $role_id
	 * @param numeric $is_default
	 * @return type
	 */
	public function setRole($operator_login_name, $user_login_name, $role_id, $is_default = 0) {
		$unique_code = \service\base\Base::buildUniqueCode($user_login_name);

		if (false === $unique_code) {
			return $this->buildResponse('error', 400, sprintf('Invalid login name "%s".', $login_name));
		}

		$data = [
			'operator_code' => \service\base\Base::buildUniqueCode($operator_login_name),
			'role_id' => $role_id,
			'is_default' => $is_default,
			'access_token' => '7NHd0yZ8ItSR6Rsj__eQpm8j_eQ6GkPMHU2j9fEO',
		];
		return self::$uc_client->send('/user/' . $unique_code . '/set-role', $data, 'POST');
	}

	/**
	 *
	 * @param type $login_name
	 * @param type $group_id
	 * @param type $attributes
	 * @return type
	 */
	public function addToGroup($login_name, $group_id, $attributes) {
		$unique_code = \service\base\Base::buildUniqueCode($login_name);

		if (false === $unique_code) {
			return $this->buildResponse('error', 400, sprintf('Invalid login name "%s".', $login_name));
		}

		$url = sprintf('/user/%s/add-to-group', $unique_code);
		$data = [
			'access_token' => '7NHd0yZ8ItSR6Rsj__eQpm8j_eQ6GkPMHU2j9fEO',
			'group_id' => $group_id,
		];

		if (!empty($attributes)) {
			$data = array_merge($attributes, $data);
		}
		return self::$uc_client->send($url, $data, 'POST');
	}

	/**
	 * Load user by user's unique_code with registered roles and groups
	 *
	 * @param type $unique_code
	 * @return type
	 */
	public function loadByCode($unique_code) {
		if (empty($unique_code) || 64 !== strlen($unique_code)) {
			return $this->buildResponse('error', 400, sprintf('Invalid user\' unique_code "%s".', $unique_code));
		}

		return self::$uc_client->send('/users/' . $unique_code, ['access_token' => '7NHd0yZ8ItSR6Rsj__eQpm8j_eQ6GkPMHU2j9fEO']);
	}

	public function challenge($login_name, $password, $type = 'password') {

		if (false === $this->isValidLoginName($login_name)) {
			return $this->buildResponse('error', 400, sprintf('Invalid login name "%s".', $login_name));
		}

		$re = self::$uc_client->send('/user/challenge', ['login_name' => $login_name, 'password' => $password], 'POST');
		\GtoolsDebug::testLog(__FILE__, [$login_name, $password, $re], __METHOD__);
		return $re;
	}

	public function update($user_code, $user_data, $operator_code) {
		if (array_key_exists('password', $user_data)) {
			return $this->buildResponse('error', 401, 'The service "user\update" does not support for updating password. ');
		}

		$user_data['operator_code'] = $operator_code;
		$api_url = sprintf('/user/%s/update', $user_code);

		$re = self::$uc_client->send($api_url, $user_data, 'POST');
		\GtoolsDebug::testLog(__FILE__, $re, __METHOD__);
		return $re;
	}

	public function updatePassword($user_code, $user_data, $auth_key, $operator_code) {
		// if (false === $this->isValidPassword($password)) {
		//  return $this->buildResponse('error', 401, 'Invalid password string.');
		// }

		// if (false === $this->isValidAuthKey($auth_key)) {
		//  return $this->buildResponse('error', 402, 'Invalid authentication key for update password.');
		// }

		$user_data['operator_code'] = $operator_code;

		$api_url = sprintf('/user/%s/update-password', $user_code);

		return self::$uc_client->send($api_url, $user_data, 'POST');
	}

	public function remove($user_code, $operator_code) {
		$user_data['operator_code'] = $operator_code;

		$api_url = sprintf('/user/%s/remove', $unique_code);

		return self::$uc_client->send($api_url, $user_data, 'POST');
	}

	/**
	 * Load user list by user's codes
	 * @param type $codes
	 * @return type
	 */
	public function loadListByCodes($codes) {
		return $this->search(['unique_code' => $codes]);
	}

	public function search($query) {
            if (!is_array($query)) {
                    return $this->buildResponse('error', 400, sprintf('Invalid query "%s".', $query));
            }

	    return self::$uc_client->send('/user/search', ['query' => base64_encode(serialize($query))], 'POST');
	}

	/**
	 *
	 *  Required parameters of $user_data
	 *  - login_name
	 *  - safe_mobile
	 *  - safe_email
	 *  - password
	 *
	 * @param type $user_data
	 * @return type
	 */
	public function register($user_data) {
		return self::$uc_client->send('/user', $user_data, 'POST');
	}

	public function getTags() {
		$data = [];
		foreach ($this->member_test_data as $m) {
			unset($m['members']);
			$data[] = $m;
		}
		return $data;
	}

	/**
	 * 为用户和企业号中的账号建立关联
	 * @param $unique_code
	 * @param $userid
	 * @return bool
	 */
	public function mappingUserid($unique_code, $userid) {
		return Mapping::create($unique_code, $userid);
	}

	/**
	 * 解除用户和企业号中账号的关联关系
	 * 取消unique_code和企业号的关联
	 * @param $unique_code
	 * @return bool|false|int
	 */
	public function unMappingUserid($unique_code) {
		return Mapping::remove($unique_code);
	}

	/**
	 * 获取用户关联的企业号userid
	 * @param $unique_code
	 * @return bool|mixed
	 */
	public function getMappedUserid($unique_code) {
		return Mapping::getUserid($unique_code);
	}

	/**
	 * @return \service\base\type
	 */
	public function buildUniqueCode($username) {
		$unique_code = Base::buildUniqueCode($username);

		if (false === $unique_code) {
			return $this->buildResponse('error', 400, sprintf('Invalid login name "%s".', $login_name));
		}
	}

	public function loadWeixin() {
		return Base::loadService('\service\user\Weixin', [], $this);
	}

	public function isValidPassword($password) {
		return (strlen($password) > 6);
	}

	public function isValidAuthKey($auth_key) {
		return true;
	}

	public function isValidLoginName($login_name) {
		return (is_string($login_name) && 60 >= strlen($login_name));
	}

	/**
	 * 向ucenter和微信通讯录中注册一个新用户，同时写入用户的关联关系
	 *
	 * @param $weixinService \service\weixin\Weixin 微信服务
	 * @param $groupid 将要被添加到的微信通讯录中的分组id
	 * @param $operator_code 执行该操作的用户的unique_code
	 * @param $userid
	 * @param $password
	 * @param string $username
	 * @param string $phone
	 * @param string $email
	 * @return \service\base\type
	 */
	public function registeUser(
		$weixinService,
		$groupid,
		$operator_code,
		$userid,
		$password,
		$username = '',
		$phone = '',
		$email = '') {

		$user_data = [
			'login_name' => $userid,
			'password' => $password,
			'user_name' => $username,
			'safe_mobile' => $phone,
			'safe_email' => $email,
		];

		$user_new = $this->register($user_data);

//        \GtoolsDebug::sysLog(__METHOD__, $user_new, __FILE__ . __LINE__);

		if (isset($user_new['code']) && in_array($user_new['code'], [200, 201, 402, 299])) {
			$insertUser = $this->search(['login_name' => $userid]);
			$user_new = $insertUser['data'][0];

			$weixinUserService = $weixinService->loadUser();
			$data['userid'] = $user_new['login_name'];
			$data['name'] = $user_new['user_name'];
			$data['department'] = $groupid;
			$data['mobile'] = $user_new['safe_mobile'];
			$data['email'] = $user_new['safe_email'];
			$res = $weixinUserService->createUser($data);

//            \GtoolsDebug::sysLog(__METHOD__, $res, __FILE__ . __LINE__);

			//无错误或已经存在时都不为错
			if (!in_array($res['errcode'], [0, 60102])) {
				//删除添加的用户
				$user_new['status'] = 'inactive';
				unset($user_new['password']);

				$this->update($user_new['unique_code'], $user_new, $operator_code);

				return $this->buildResponse('error', 409, $res['errmsg']);
			}

			//写入mapping
			$unique_code = $this->loadWeixin()->createMap($user_new['unique_code'], $user_new['login_name']);
			return $this->buildResponse('success', 200, '');
		} else {
			return $this->buildResponse($user_new['status'], $user_new['code'], $user_new['data']);
		}
	}
}
