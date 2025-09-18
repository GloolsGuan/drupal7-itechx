<?php

/**
 * 学员模块
 * 学员报名状态:0：非付费学员，1：已付费，2：审核通过
 */

namespace service\plan\interfaces;

/**
 * Interface Member
 * @package service\plan\interfaces
 * @design yangzy 20161122
 * @author shenf 20161125
 */
interface Member
{
    /**
     * 设置成员状态
     *
     * @param int $participantId 成员id
     * @param int $status -1：删除；1：正常；2：禁用
     * @return 成功时返回true
     */
    public function setStatus($participantId, $status);

    /**
     * 获取用户参与的培训计划
     *
     * @param string $unique_code 用户unique_code
     * @param array $where 数据过滤条件，格式参照 yii2 where()参数
     * @param int $offset
     * @param int $limit
     * @param string $order
     * @return array 【1】 需要包含用户支付该课程的金额
     *[
     * [
     * 'id' => 1,//计划id
     * 'name' => '计划',//计划名称
     * 'price' => 100,//课程费用
     * 'type' => '培训',//计划类型
     * 'beginTime' => '2016-11-08 13:58:41',//计划开始时间
     * 'endTime' => '2016-11-08 13:58:41',//计划结束时间
     * 'status' => 2,//计划状态
     * 'credit' => 10,//课程设置的学分
     * 'userCredit' => 8,//用户获取的学分
     * 'pay' => 100,//用户支付该培训课程的金额，不是通过订购加入培训课程的用户金额为0
     * ],
     * [
     * 'id' => 2,//计划id
     * 'name' => '计划',//计划名称
     * 'price' => 100,//课程费用
     * 'type' => '培训',//计划类型
     * 'beginTime' => '2016-11-08 13:58:41',//计划开始时间
     * 'endTime' => '2016-11-08 13:58:41',//计划结束时间
     * 'status' => 2,//计划状态
     * 'credit' => 10,//课程设置的学分
     * 'userCredit' => 8,//用户获取的学分
     * 'pay' => 100,//用户支付该培训课程的金额，不是通过订购加入培训课程的用户金额为0
     * ]
     * ]
     * @design yangzy 20161207
     * @author shenf 20161208
     */
    public function getMemberPlansData($unique_code, array $where = [], $offset = 0, $limit = 20, $order = '');

    /**
     * 获取用户参与的任务【暂不实现】
     *
     * @param string $unique_code 用户unique_code
     * @param array $where 数据过滤条件，格式参照 yii2 where()参数
     * @param int $offset
     * @param int $limit
     * @param string $order
     * @return array 【1】 需要包含用户任务的完成情况
     * [
     * [
     * 'id' => 1,//任务id
     * 'name' => '任务',//任务名称
     * 'beginTime' => '2016-11-08 13:58:41',//任务开始时间
     * 'endTime' => '2016-11-08 13:58:41',//任务结束时间
     * 'status' => 1,//任务状态
     * 'doneTime' => '2016-11-10 13:58:41',//用户完成该任务的日期
     * ],
     * [
     * 'id' => 2,//任务id
     * 'name' => '任务',//任务名称
     * 'beginTime' => '2016-11-08 13:58:41',//任务开始时间
     * 'endTime' => '2016-11-08 13:58:41',//任务结束时间
     * 'status' => 1,//任务状态
     * 'doneTime' => '2016-11-10 13:58:41',//用户完成该任务的日期
     * ]
     * ]
     * @design yangzy 20161208
     * @author shenf 20161209
     */
    public function getMemberTasks($unique_code, array $where = [], $offset = 0, $limit = 20, $order = '');

    /**
     * 获取用户参与的调研
     *
     * @param string $unique_code 用户unique_code
     * @param array $where 数据过滤条件，格式参照 yii2 where()参数
     * @param int $offset
     * @param int $limit
     * @param string $order
     * @return array 【1】 需要包含用户调研的完成情况
     * [
     * [
     * 'id' => 1,//调研id
     * 'name' => '调研',//调研名称
     * 'doneTime' => '2016-11-10 13:58:41',//用户完成该调研的日期
     * ],
     * [
     * 'id' => 2,//调研id
     * 'name' => '调研',//调研名称
     * 'doneTime' => '2016-11-10 13:58:41',//用户完成该调研的日期
     * ]
     * ]
     * @design yangzy 20161208
     * @author shenf 20161209
     */
    public function getMemberSurveys($unique_code, array $where = [], $offset = 0, $limit = 20, $order = '');

    /**
     * 获取用户获得的积分
     *
     * @param string $unique_code 用户unique_code
     * @param array $where 数据过滤条件，格式参照 yii2 where()参数，需要支持时间段过滤
     * @param int $offset
     * @param int $limit
     * @param string $order
     * @return array 【1】 需要包含用户调研的完成情况
     *  [
     * [
     * 'id' => 1,//积分记录id
     * 'time' => '2016-11-10 13:58:41',//用户获得积分的时间
     * 'memo' => '完成xxxx培训',//该积分的描述
     * ],
     * [
     * 'id' => 2,//积分记录id
     * 'time' => '2016-11-10 13:58:41',//用户获得积分的时间
     * 'memo' => '完成xxxx考试',//该积分的描述
     * ],
     * ]
     * @design yangzy 20161208
     * @author shenf 20161209
     */
    public function getMemberCredit($unique_code, array $where = [], $offset = 0, $limit = 20, $order = '');
}