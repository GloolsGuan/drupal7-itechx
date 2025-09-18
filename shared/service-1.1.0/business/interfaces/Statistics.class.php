<?php
/**
 *
 */

namespace service\business\interfaces;


interface Statistics
{
    /**
     * 财务订单
     *需要支持订单号、用户名、用户单位搜索
     *
     * @param array $map 过滤条件，格式参照yii2 where()方法参数
     * @param string $group 分组统计：[不分组、个人、部门、课程]
     * @param int $offset
     * @param int $limit
     * @return array 订单统计数据
     * [
     * [
     * 'id' => 1,//订单表id
     * 'order_no' => '413434q768765433',//订单号
     * 'create_time' => '2016-12-08 21:00:04',//订单创建时间
     * 'pay_time' => '2016-12-08 21:00:04',//支付时间
     * 'goods' => [
     * [
     * 'id' => 2,//订单商品表id
     * 'course_id' => 12,//计划id
     * 'title' => '计划名称',//计划名称
     * ]
     * ],
     * 'status' => 1,//订单状态
     * 'amount' => 1000,//支付金额
     * 'unique_code' => 'fssjfdhfd',//订购用户的unique_code
     * 'user_name' => '小王',//订购用户的用户名
     * 'corporation' => '岱恩教育',//机构名称
     * ],
     * [
     * 'id' => 2,//订单表id
     * 'order_no' => '413434q768765433',//订单号
     * 'create_time' => '2016-12-08 21:00:04',//订单创建时间
     * 'pay_time' => '2016-12-08 21:00:04',//支付时间
     * 'goods' => [
     * [
     * 'id' => 2,//订单商品表id
     * 'course_id' => 12,//计划id
     * 'title' => '计划名称',//计划名称
     * ]
     * ],
     * 'status' => 1,//订单状态
     * 'amount' => 1000,//支付金额
     * 'unique_code' => 'fssjfdhfd',//订购用户的unique_code
     * 'user_name' => '小李',//订购用户的用户名
     * 'corporation' => '岱恩教育',//机构名称
     * ]
     * ]
     */
    public function statistics(array $map = [], $group = '', $offset = 0, $limit = 20);
}