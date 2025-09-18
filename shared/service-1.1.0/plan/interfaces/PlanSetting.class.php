<?php
/**
 *计划的配置，使用数据表pl_setting
 */

namespace service\plan\interfaces;

/**
 * Interface PlanSetting
 * @package service\plan\interfaces
 * @design yangzy 20161125
 * @author shenf 20161128
 */
interface PlanSetting
{
    /**
     * 保存计划设置
     *
     * @param int $planId 计划id
     * @param array $data 设置信息
     * @return 成功返回true [1]
     */
    public function saveSetting($planId, array $data);

    /**
     * 获取计划的配置
     *
     * @param int $planId 计划id
     * @return 成功时返回计划的配置 [1]
     */
    public function getSetting($planId);
}