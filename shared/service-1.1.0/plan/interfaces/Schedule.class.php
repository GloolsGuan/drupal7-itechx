<?php
/**
 *
 */

namespace service\plan\interfaces;

/**
 * Interface Schedule
 * 概念：
 * 子日程：
 * 一个日程下可以添加多个子日程（暂时只支持两级，即子日程下不能再添加子日程）
 * 当父日程有签到设置时，子日程将不再需要签到操作
 *
 * @package service\plan\interfaces
 * @design yangzy 20161201
 * @author shenf 20161202
 */
interface Schedule
{
    /**
     * 创建日程
     *
     * @param array $schedule 日程数据 格式参照plan数据表
     * @return 成功时返回新创建日程的数据[1]
     */
    public function createSchedule(array $schedule);

    /**
     * 更新日程
     *
     * @param array $schedule 日程数据 格式参照plan数据表
     * @return 成功时返回更新后的日程的数据[1]
     */
    public function updateSchedule(array $schedule);

    /**
     * 获取子日程
     *
     * @param int $scheduleId 日程id
     * @return 子日程列表[1]
     */
    public function getSubSchedule($scheduleId);
}