<?php
/**
 *
 */

namespace service\plan\interfaces;

/**
 * Interface Statistics
 * 统计接口
 *
 * @package service\plan\interfaces
 * @design yangzy 20161206
 * @design shenf
 */
interface Statistics
{
    /**
     *计划的综合统计数据
     *
     * @param int $planId 要统计的计划的id
     * @return array [1] 统计内容如下：
     * [
     * 'id'=>1,//计划id
     * 'views' => 1000,//计划的浏览量
     * 'confirmed' => 80,//确认参与计划人数
     * 'participants' => 100,//参与计划人数
     * 'schedules' => [//计划的日程信息，不包括子日程
     * [
     * 'id'=>1,//日程id
     * 'name' => '日程1',//日程名称
     * 'signCount' => 60,//签到人数
     * ]
     * ],
     * 'tasks' => [//计划关联的调研、资料等统计情况
     * 'survey' => [//调研信息
     * [
     * 'title' => '调研1',
     * 'participants' => 50//参与人数
     * ],
     * [
     * 'title' => '调研2',
     * 'participants' => 60//参与人数
     * ]
     * ],
     * 'document' => [//资料信息，暂不处理
     * [
     * 'title' => '调研1',
     * 'participants' => 50//参与人数
     * ],
     * [
     * 'title' => '调研2',
     * 'participants' => 60//参与人数
     * ]
     * ]
     * ]
     * ]
     */
    public function overview($planId);

    /**
     * 统计用户
     *
     * @param int $planId 计划id
     * @param array $userIds 用户id组成的数组，当不为空时，查询对应的用户统计信息，为空时表示查询计划中所有用户
     * @param string $name 搜索名称，模糊搜索
     * @return array
     *  [
     * 'id' => 1,//计划id
     * 'signCount' => 6,//计划中含有的签到总数
     * 'participants' => [//计划参与人员分组
     * [
     * 'name' => '小李',//姓名
     * 'group' => [//所属小组
     * 'id' => 2,//小组id
     * 'name' => 'A组',
     * ],
     * 'signCount' => 5,//签到次数
     * 'downloadCount' => 4,//下载资料数
     * 'surveyCount' => 5,//调研次数
     * 'credit' => 100,//积分
     * ],
     * [
     * 'name' => '小李',//姓名
     * 'group' => [//所属小组
     * 'id' => 2,//小组id
     * 'name' => 'A组',
     * ],
     * 'signCount' => 5,//签到次数
     * 'downloadCount' => 4,//下载资料数
     * 'surveyCount' => 5,//调研次数
     * 'credit' => 100,//积分
     * ]
     * ]
     * ]
     */
    public function userStatistics($planId, array $userIds = [], $name = '');

    /**
     * 小组统计
     *
     * @param int $planId 计划id
     * @return array
     * [
     * 'id' => 1,//计划id
     * 'groups' => [//计划参与人员分组
     * [
     * 'name' => 'A小组',//分组名称
     * 'credit' => 100,//小组积分
     * 'rank' => 1,//积分排名
     * 'participants' => [//小组成员
     * [
     * 'id' => 'xxxxxxxxxxxxxx',//成员id
     * 'name' => '大刘',//成员名
     * 'credit' => 50,//学分
     * ], [
     * 'id' => 'xxxxxxxxxxxxxx',//成员id
     * 'name' => '大李',//成员名
     * 'credit' => 50,//学分
     * ],
     * ]
     * ],
     * [
     * 'name' => 'D小组',//分组名称
     * 'credit' => 50,//小组积分
     * 'rank' => 2,//积分排名
     * 'participants' => [//小组成员
     * [
     * 'id' => 'xxxxxxxxxxxxxx',//成员id
     * 'name' => '小刘',//成员名
     * 'credit' => 50,//学分
     * ]
     * ]
     * ]
     * ]
     * ]
     */
    public function userGroupStatistics($planId);

    /**
     * 统计签到
     * @param int $planId 计划id
     * @return array 统计内容
     * [
     * 'id' => 1,//计划id
     * 'views' => 1000,//计划的浏览量
     * 'confirmed' => 80,//确认参与计划人数
     * 'participants' => 100,//参与计划人数
     * 'schedules' => [//计划的日程信息，不包括子日程
     * [
     * 'id' => 1,//日程id
     * 'name' => '日程1',//日程名称
     * 'signType' => 1,//签到方式,0：不签到，1：手写签到，2：快捷签到
     * 'signCount' => 60,//签到人数
     * ],
     * ]
     * ]
     */
    public function signStatistics($planId);

    /**
     * 日程统计信息
     *
     * @param int $scheduleId 日程id
     * @return array
     *[
     * 'id' => 1,//日程id
     * 'participants' => 100,//日程的参与人数，目前等于计划的参与人数
     * 'signType' => 1,//签到方式,0：不签到，1：手写签到，2：快捷签到
     * 'signed' => 80,//该日程签到人数
     * 'beginTime' => '2016-11-25 15:36:17',//该日程开始时间
     * 'endTime' => '2016-11-25 15:36:17',//该日程结束时间
     * 'signAddress' => '南京金蝶软件园',//日程签到地点
     * 'participantsData' => [//人员的信息
     * [
     * 'id' => 1,//用户id
     * 'name' => '小张',//用户名
     * 'corporation' => 'XXX公司',//公司名称
     * 'department' => '宇宙部',//部门
     * 'position' => '大将军',//职务
     * 'signDate' => '2016-11-04 15:03:53',//签到时间
     * 'signAddress' => '南京金蝶软件园',//签到地点
     * 'signPicture' => '签到图片地址',//签名图片，例【/upload/sign/20161129032600126.png】
     * 'meta' => '签到备注信息'//签到备注信息
     * ], [
     * 'id' => 2,//用户id
     * 'name' => '小王',//用户名
     * 'corporation' => 'XXX公司',//公司名称
     * 'department' => '宇宙部',//部门
     * 'position' => '大将军',//职务
     * 'signDate' => '',//签到时间
     * 'signAddress' => '',//签到地点
     * 'signPicture' => '',//签名图片，例【/upload/sign/20161129032600126.png】
     * 'meta' => ''//签到备注信息
     * ],
     * ]
     * ]
     */
    public function scheduleStatistics($scheduleId, array $map = []);

    /**
     * 统计计划所关联的任务【暂不实现】
     *
     * @param int $planId 计划id
     * @return array
     * [
     * 'id' => 1,//计划id
     * 'participants' => 100,//计划的参与人数
     * 'survey' => [//关联的调研
     * [
     * 'id' => 1,//调研id
     * 'title' => '小调查',//调研名称
     * 'doneCount' => 20,//完成人数
     * ],
     * [
     * 'id' => 2,//调研id
     * 'title' => '调查2',//调研名称
     * 'doneCount' => 30,//完成人数
     * ]
     * ],
     * ]
     */
    public function tasksStatistics($planId);

    /**
     * 统计任务的信息【暂不实现】
     *
     * @param int $taskId 任务id
     * @return mixed
     *[
     * 'id' => 1,//任务id
     * 'name' => '调查研究',//任务名称
     * 'participants' => 100,//任务的参与人数，目前等于计划的参与人数
     * 'participantsData' => [//关联的调研
     * [
     * 'id' => 1,//用户id
     * 'name' => '小王',//用户名称
     * 'doneStatus' => 1,//任务完成发问，1：已完成，0：未完成
     * 'doneAt' => '2016-11-07 10:30:59',//完成时间
     * ],
     * [
     * 'id' => 2,//用户id
     * 'name' => '小王2',//用户名称
     * 'doneStatus' => 0,//任务完成发问，1：已完成，0：未完成
     * 'doneAt' => '2016-10-07 10:30:59',//完成时间
     * ],
     * ],
     * ]
     */
    public function taskStatistics($taskId);

    /**
     * 调研统计
     *
     * @param int $planId 计划id
     * @return array
     * [
     * 'id' => 1,//计划id
     * 'participants' => 100,//计划的参与人数
     * 'survey' => [//关联的调研
     * [
     * 'id' => 1,//调研id
     * 'title' => '小调查',//调研名称
     * 'doneCount' => 20,//完成人数
     * ],
     * [
     * 'id' => 2,//调研id
     * 'title' => '调查2',//调研名称
     * 'doneCount' => 30,//完成人数
     * ]
     * ],
     * ]
     */
    public function surveysStatistics($planId);

    /**
     * 调研统计，对各题的统计【暂不实现】
     *
     * @param int $surveyId 调研id
     * @return array
     *
     */
    public function surveyStatistics($surveyId);

    /**
     * 订单统计
     *
     * @param int $planId 计划id
     * @param string $search 当不为空时，将模糊匹配用户名和订单号
     * @param string $department 当不为空时，将查询对应的部门的订单【暂不实现】
     * @return array
     * [
     * 'id' => 1,//计划id
     * 'orders' => [//订单
     * [
     * 'id' => 1,//订单id
     * 'orderid' => '214313',//订单号
     * 'userid' => 'lkdairna52ikssd',//用户id
     * 'username' => '小王',//用户名
     * 'createAt' => '2016-12-05 14:04:41',//交易日期
     * 'amount' => 10000,//订单总额
     * 'reason' => 'hello world',//备注
     * ],
     * [
     * 'id' => 2,//订单id
     * 'orderid' => '2143335',//订单号
     * 'userid' => 'ldddssaafaa52ikssd',//用户id
     * 'username' => '小王2',//用户名
     * 'createAt' => '2016-12-05 14:04:41',//交易日期
     * 'amount' => 10000,//订单总额
     * 'reason' => 'hello world',//备注
     * ]
     * ]
     * ]
     */
    public function orderStatistics($planId, array $search = [], $department = '');

    /**
     * 学分统计
     *
     * @param int $planId 计划id
     * @param array $userIds 用户id组成的数组，当不为空时，查询对应的用户统计信息，为空时表示查询计划中所有用户
     * @param string $name 搜索名称，模糊搜索
     * @return array
     * [
     * 'id' => 1,//计划id
     * 'participants' => [//计划学员
     * [
     * 'id' => 'lkdairna52ikssd',//用户id
     * 'name' => '小王',//用户名
     * 'group' => [//交易日期
     * 'id' => 1,//小组id
     * 'name' => 'A组'
     * ],
     * 'courseCredit' => 10,//课程评分
     * 'surveyCredit' => 15,//调研评分
     * 'totalCredit' => 210,//总学分
     * ]
     * ]
     * ]
     */
    public function creditStatistics($planId, array $userIds = [], $name = '');

    /**
     *所有计划统计信息
     *
     * @param string $nameSearch 模糊查询计划名称
     * @param int $offset
     * @param int $limit
     * @return array 【1】
     * [
     * [
     * 'id' => 1,
     * 'title' => '活动名1',//活动名称
     * 'createTime' => '2016-12-08 11:02:58',//创建时间
     * 'updateTime' => '2016-12-08 11:02:58',//更新时间
     * 'beginTime' => '2016-12-08 11:02:58',//开始时间
     * 'endTime' => '2016-12-08 11:02:58',//结束时间
     * 'type' => 1,//活动类型
     * 'address' => '活动地址',//活动地址
     * 'credit' => 10,//课程学分
     * 'memberCount' => 90,//成员数
     * 'memberLimited' => 100,//课程名额
     * 'payCount' => 110,//缴费人数
     * 'refundCount' => 20,//退费人数
     * 'payTotal' => 1002000,//总收入
     * 'refundTotal' => 2000,//总退费
     * ],
     * [
     * 'id' => 2,
     * 'title' => '活动名2',//活动名称
     * 'createTime' => '2016-12-08 11:02:58',//创建时间
     * 'updateTime' => '2016-12-08 11:02:58',//更新时间
     * 'beginTime' => '2016-12-08 11:02:58',//开始时间
     * 'endTime' => '2016-12-08 11:02:58',//结束时间
     * 'type' => 1,//活动类型
     * 'address' => '活动地址',//活动地址
     * 'credit' => 10,//课程学分
     * 'memberCount' => 90,//成员数
     * 'memberLimited' => 100,//课程名额
     * 'payCount' => 110,//缴费人数
     * 'refundCount' => 20,//退费人数
     * 'payTotal' => 1002000,//总收入
     * 'refundTotal' => 2000,//总退费
     * ]
     * ]
     */
    public function planStatistics($nameSearch = '', $offset = 0, $limit = 20);
}
