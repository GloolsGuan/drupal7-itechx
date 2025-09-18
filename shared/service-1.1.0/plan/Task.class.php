<?php
/**
 *
 */

namespace service\plan;

use service\base\Module;
use service\plan\interfaces\PlanTask;

/**
 * Class Task
 * @package service\plan
 * @author
 */
class Task extends Module implements PlanTask
{
    /**
     * 保存/更新任务信息
     *
     * @param array $taskData 任务的信息
     * @return array 【1】 成功时返回存储后的任务信息
     */
    public function saveTask(array $taskData)
    {
        $data = [
            'id' => 1, //主键
            'plan_id' => 3,//所属计划
            'type' => 1,//任务类型，日程、调研、考试等
            'detail_id' => 12, //任务原始id
            'name' => '任务名称',//任务名称
            'required' => 1,//是否必须完成
            'status' => 1,//状态，已删除、未删除
        ];

        return $this->buildResponse('success', 201, $data);
    }

    /**
     * 查询任务列表
     *
     * @param array $map 过滤条件，格式参照yii2 where()方法
     * @param int $offset 数据偏移量
     * @param int $limit 数据条数
     * @return array 成功时返回任务列表 [1]
     */
    public function listTasks(array $map = [], $offset = -1, $limit = -1)
    {
        $data = [
            [
                'id' => 1, //主键
                'plan_id' => 3,//所属计划
                'type' => 1,//任务类型，日程、调研、考试等
                'detail_id' => 12, //任务原始id
                'name' => '任务名称',//任务名称
                'required' => 1,//是否必须完成
                'status' => 1,//状态，已删除、未删除
            ], [
                'id' => 2, //主键
                'plan_id' => 3,//所属计划
                'type' => 2,//任务类型，日程、调研、考试等
                'detail_id' => 1, //任务原始id
                'name' => '任务名称',//任务名称
                'required' => 0,//是否必须完成
                'status' => 1,//状态，已删除、未删除
            ]
        ];

        return $this->buildResponse('success', 201, $data);
    }

    /**
     * 设置任务表中任务的完成条件
     *
     * @param array $ids 任务表id
     * @param int $required 是否必须完成,默认为非必须完成（0）
     * @return array 成功时返回更新后的任务信息,[1]，数据格式同listTasks()
     */
    public function setRequired(array $ids = [], $required = 0)
    {
        $data = [
            [
                'id' => 1, //主键
                'plan_id' => 3,//所属计划
                'type' => 1,//任务类型，日程、调研、考试等
                'detail_id' => 12, //任务原始id
                'name' => '任务名称',//任务名称
                'required' => 1,//是否必须完成
                'status' => 1,//状态，已删除、未删除
            ], [
                'id' => 2, //主键
                'plan_id' => 3,//所属计划
                'type' => 2,//任务类型，日程、调研、考试等
                'detail_id' => 1, //任务原始id
                'name' => '任务名称',//任务名称
                'required' => 0,//是否必须完成
                'status' => 1,//状态，已删除、未删除
            ]
        ];

        return $this->buildResponse('success', 201, $data);
    }


}