<?php
/**
 *
 */

namespace service\user\interfaces;

/**
 * Interface Blacklist
 * 黑名单功能，相关数据表usr_blacklist
 *
 * @package service\user\interfaces
 * @design yangzy 20161207
 * @author zhuzf 20161209
 */
interface Blacklist
{
    /**
     *获取黑名单中的列表
     *
     * @param string $search 不为空时，用于模糊匹配 用户名和手机号
     * @param int $offset
     * @param int $limit
     * @return array 【1】
     *用户数据结构
     * [
     * [
     * 'id' => 1,//黑名单记录id
     * 'create_time' => '2016-11-04 11:50:27',//加入黑名单的日期
     * 'operator' => '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', //操作者unique_code
     * 'unique_code' => '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918',//被加入黑名单用户的unique_code
     * 'username' => 'user1',//黑名单用户名
     * 'phone' => '123456789',//用户手机
     * 'reason' => 'no reason'//操作理由
     * ],
     * [
     * 'id' => 2,//黑名单记录id
     * 'create_time' => '2016-11-04 11:50:27',//加入黑名单的日期
     * 'operator' => '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', //操作者unique_code
     * 'unique_code' => '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918',//被加入黑名单用户的unique_code
     * 'username' => 'user2',//黑名单用户名
     * 'phone' => '123456789',//用户手机
     * 'reason' => 'no reason'//操作理由
     * ]
     * ]
     */
    public function getUsers($search = '', $offset = 0, $limit = 20);

    /**
     * 将用户加入黑名单
     *
     * @param array $usersData 用户数据
     * [
     * ['unique_code'=>'b5410415bde908bd4dee15','reason'=>'理由'],
     * ['unique_code'=>'b5410415bde908bd433335','reason'=>'理由2'],
     * ]
     * @param string $operator 操作人unique_code
     * @return array 【1】返回加入后的用户的数据
     *用户数据结构
     * [
     * [
     * 'id' => 3,//黑名单记录id
     * 'create_time' => '2016-11-04 11:50:27',//加入黑名单的日期
     * 'operator' => '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', //操作者unique_code
     * 'unique_code' => '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918',//被加入黑名单用户的unique_code
     * 'username' => 'user3',//黑名单用户名
     * 'phone' => '123456789',//用户手机
     * 'reason' => 'no reason'//操作理由
     * ]
     * ]
     */
    public function insertUsers(array $usersData, $operator);

    /**
     * 将用户从黑名单中移除
     *
     * @param array $userIds 用户unique_code组成的数组
     * @return array【1】成功返回true
     */
    public function removeUsers(array $userIds);
}