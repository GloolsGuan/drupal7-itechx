<?php
/**
 * 计划操作模块
 */

namespace service\plan\interfaces;


/**
 * 计划交易接口
 *
 * @package service\plan\interfaces
 * @design yangzy 20161122
 * @author liurw 20161123
 */
interface PlanBusiness
{
    /**
     * 设置计划的报名配置
     *
     * @param int $planId 计划的id
     * @param array $setting 计划的报名设置
     * @return boolean|array 成功时返回true
     */
    public function setEnrollSetting($planId, array $setting = []);

}