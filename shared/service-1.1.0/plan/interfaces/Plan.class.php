<?php
/**
 *
 */

namespace service\plan\interfaces;

/**
 * Interface Plan
 *
 * @package service\plan\interfaces
 * @design yangzy 20161206
 * @author shenf 20161207
 */
interface Plan
{
    /**
     * 结业检查，当课程计划结束时，执行该方法对学员进行学业完成情况检查，并标记participant表completion字段
     *
     * @return array 成功时返回true【1】
     */
    public function courseCheck($planId, $participantId);

    /**
     * 获取课程所包含的课程作业，包含日程、调研等
     * 同时获取课程所对应的结业条件设置[pl_setting/completion_setting],将对应设置合并到课程作业列表数据中
     * tips:存/取结业条件设置可以使用PlanSetting接口的saveSetting()和getSetting()方法
     *
     * @param int $planId 计划id
     * @return array 返回计划包含的日程和调研组成的列表数据
     */
    public function listCourseWorks($planId);

    /**
     * 查询指定计划
     *
     * @param array $planIds 计划id组成的数组
     * @return array 计划列表 【1】
     */
    public function getPlansById(array $planIds = []);
}