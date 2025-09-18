<?php
/**
 * 计划任务模块
 * 相关数据表：pl_task(计划任务表), pl_task_log(计划详细完成情况表)
 * 任务:被关联到计划中的日程、调研、考试、资料等都是一项任务
 * 当一个任务被添加到计划时，应同时在任务表中插入一条任务；更新/删除时同时更新/删除计划任务表信息
 * 当计划结束时,系统将根据计划任务表中的任务的完成条件设置判定用户是否完成的该计划和是否给予用户学分
 */

namespace service\plan\interfaces;

/**
 * Interface PlanTask
 * @package service\plan\interfaces
 * @design yangzy 20161220
 */
interface PlanTask
{
    /**
     * 保存/更新任务信息
     *
     * @param array $taskData 任务的信息
     * @return array 【1】 成功时返回存储后的任务信息
     * [
     * 'id' => 1, //主键
     * 'plan_id' => 3,//所属计划
     * 'type' => 1,//任务类型，日程、调研、考试等
     * 'detail_id' => 12, //任务原始id
     * 'name' => '任务名称',//任务名称
     * 'required' => 1,//是否必须完成
     * 'status' => 1,//状态，已删除、未删除
     * ]
     */
    public function saveTask(array $taskData);

    /**
     * 查询任务列表
     *
     * @param array $map 过滤条件，格式参照yii2 where()方法
     * @param int $offset 数据偏移量
     * @param int $limit 数据条数
     * @return array 成功时返回任务列表 【1】
     * [
     * [
     * 'id' => 1, //主键
     * 'plan_id' => 3,//所属计划
     * 'type' => 1,//任务类型，日程、调研、考试等
     * 'detail_id' => 12, //任务原始id
     * 'name' => '任务名称',//任务名称
     * 'required' => 1,//是否必须完成
     * 'status' => 1,//状态，已删除、未删除
     * ], [
     * 'id' => 2, //主键
     * 'plan_id' => 3,//所属计划
     * 'type' => 2,//任务类型，日程、调研、考试等
     * 'detail_id' => 1, //任务原始id
     * 'name' => '任务名称',//任务名称
     * 'required' => 0,//是否必须完成
     * 'status' => 1,//状态，已删除、未删除
     * ]
     * ]
     */
    public function listTasks(array $map = [], $offset = -1, $limit = -1);

    /**
     * 设置任务表中任务的完成条件
     *
     * @param array $ids 任务表id
     * @param int $required 是否必须完成,默认为非必须完成（0）
     * @return array 成功时返回更新后的任务信息,[1]，数据格式同listTasks()
     */
    public function setRequired(array $ids = [], $required = 0);
}