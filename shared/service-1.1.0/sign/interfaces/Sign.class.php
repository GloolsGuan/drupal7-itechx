<?php
/**
 *
 */

namespace service\sign\interfaces;


interface Sign
{
    /**
     * 查询签到数据
     *
     * @param array $map 查询条件，格式参照where()方法参数
     * @param int $offset -1表示不偏移
     * @param int $limit -1表没有条数限制
     * @return array 【注解1】 返回sign_detail表数据[
     * [
     * 'sign_detail_id' => 1,//表主键
     * 'sign_id' => 2,//签到id
     * 'data' => '更多数据',//扩展数据
     * 'create_time' => '2016-12-18 10:32:01', //数据记录时间
     * 'unique_code' => '27a5bd6f3a91b31869dc205455f9d557e9c6b9c81a43bc9f91d757ac3a4f4e8c',//签到人用户id
     * 'user_name' => 'xman',//签到者用户名
     * 'draw_url' => '/upload/sign/20161214045318522.png',//手写签到图片
     * 'longitude' => '118.82972127911',//经度
     * 'latitude' => '32.044362808336', //纬度
     * 'address' => '康家沟',//签到地址
     * 'meta' => '你好',//用户留言
     * ],
     * [
     * 'sign_detail_id' => 2,//表主键
     * 'sign_id' => 2,//签到id
     * 'data' => '更多数据',//扩展数据
     * 'create_time' => '2016-12-18 10:32:01', //数据记录时间
     * 'unique_code' => '27a5bd6f3a91b31869dc205455f9d557e9c6b9c81a43bc9f91d757ac3a4f4e8c',//签到人用户id
     * 'user_name' => 'xman',//签到者用户名
     * 'draw_url' => '/upload/sign/20161214045318522.png',//手写签到图片
     * 'longitude' => '118.82972127911',//经度
     * 'latitude' => '32.044362808336', //纬度
     * 'address' => '康家沟',//签到地址
     * 'meta' => '你好',//用户留言
     * ],
     * ]
     * @design yangzy 201218
     */
    public function searchSignData(array $map = [], $offset = -1, $limit = -1);

    /**
     * 查询用户的签到数据
     *
     * @param string|array $userId 用户unique_code或unique_code组成的数组
     * @param array $map 同searchSignData()
     * @param int $offset 同searchSignData()
     * @param int $limit 同searchSignData()
     * @return  同searchSignData()
     * @design yangzy 201218
     */
    public function searchUserSignData($userId, array $map = [], $offset = -1, $limit = -1);

    /**
     * 计算用户签到次数
     *
     * @param string $userId 用户unique_code
     * @return array 【1】 返回用户签到次数
     * @design yangzy 201218
     */
    public function getUserSignCount($userId);
}