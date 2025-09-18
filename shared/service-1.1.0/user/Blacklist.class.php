<?php
/**
 *
 */

namespace service\user;

use service\base\Module;
use service\user\interfaces\Blacklist as BlacklistInterface;
use service\user\models\UsrBlacklist as ModBlacklist;
use service\base\Base;
use yii\helpers\ArrayHelper;

class Blacklist extends Module implements BlacklistInterface
{
    /**
     * @inheritDoc
     */
    public function getUsers($search = '', $offset = 0, $limit = 20)
    {

        $data = $this->getUsersByCodes();

        if (false == $data) return;
//获取操作者信息
        $operatorCodes = array_column($data, 'operator');
        $operators = Base::loadService('service\user\User')->search(['unique_code'=>$operatorCodes]);

        $operators = ArrayHelper::map($operators['data'], 'unique_code', 'user_name');

        foreach ($data as $key => &$item)
            $item['operator_name'] = isset($operators[$item['operator']]) ? $operators[$item['operator']] : '';
//获取详情
        $unique_codes = array_column($data, 'unique_code');
        $users = Base::loadService('service\user\User')->search(['unique_code'=>$unique_codes]);

        $merge = array_merge($users['data'], $data);

        $lists = [];
        foreach ($merge as $key => $value)
            $lists[$value['unique_code']][] = $value;

        $res = [];
        foreach ($lists as &$list)
            $res[] = array_merge($list[0], $list[1]);


//用户筛选
        if (!empty($search)) {
            $match = [];
            foreach ($res as $re) {
                if (strstr($re['user_name'], $search) || strstr($re['safe_mobile'], $search)) {
                    $match[] = $re;
                }
            }
            return $match;
        }

        return $res;
    }

    public function getUsersByCodes(array $uniqueCodes = [])
    {
        $where = ['unique_code' => $uniqueCodes];

        return ModBlacklist::getBlacklists($where);
    }

    /**
     * @inheritDoc
     */
    public function insertUsers(array $usersData, $operator)
    {
        foreach ($usersData as $item)
            if (empty($item['unique_code'])) return $this->buildResponse('error', 400, 'unique_code cannot be empty');

        if (empty($operator)) return $this->buildResponse('error', 400, 'operator cannot be empty');

        $unique_codes = array_column($usersData, 'unique_code');
        $reasons = array_column($usersData, 'reason');

        $users = Base::loadService('service\user\User')->search(['unique_code' => $unique_codes]);
        if (empty($users['data'])) return $this->buildResponse('error', 400, 'users is not exit');

        ModBlacklist::addBlacklist($users['data'], $reasons, $operator);

        $data = $this->getUsersByCodes($unique_codes);

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function removeUsers(array $userIds)
    {
        return ModBlacklist::deleteAll(['unique_code' => $userIds]);
    }
}