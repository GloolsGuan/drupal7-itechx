<?php
/**
 *
 */

namespace service\plan\interfaces;

/**
 * Interface Survey
 * 计划的调研问卷表
 *
 * @package service\plan\interfaces
 * @design yangzy 20161201
 */
interface Survey
{
    /**
     * 获取计划所关联的调研
     *
     * @param int $planId 计划的id
     * @param array $map 过滤条件
     * @param int $offset 偏移量
     * @param int $limit 条数
     * @return array 成功时返回调研列表 [1]
     */
    public function getSurveys($planId, array $map = [], $offset = -1, $limit = -1);

    /**
     * 获取计划调研的设置
     *
     * @param $planSurveyId
     * @return array 【1】计划调研信息
     */
    public function getSurveySetting($planSurveyId);

    /**
     * 设置计划调研
     *
     * @param $planSurveyId
     * @return array 【1】返回新的设置信息
     */
    public function setSurveySetting($planSurveyId, array $setting);

    /**
     * 给计划关联调研
     *
     * @param int $planId 计划id
     * @param array $surveyIds 调研id组成的数组
     * @return 成功时true [1]
     */
    public function addSurveys($planId, array $surveyIds);

    /**
     * 取消计划与调研的关联
     *
     * @param array $planSurveyIds 计划调研表id组成的数组
     * @return 成功时返回被删除的计划调研表id组成的数组 【1】
     */
    public function removeSurveys(array $planSurveyIds);

    /**
     *设置计划调研
     *
     * @param $planSurveyId 计划调研表id
     * @param array $planSurveyInfo 配置信息
     * @return 成功时返回计划调研信息【1】
     */
    public function setSurvey($planSurveyId, array $planSurveyInfo);

    /**
     * 设置调研的标签
     * @param int $planSurveyId 调研id
     * @param array $tagIds 调研标签id组成的数组，当数据为空时即表示删除调研的标签
     * @return array 成功时返回被添加的标签数据【1】
     */
    public function setTags($planSurveyId, array $tagIds = []);

    /**
     * 获取调研关联的标签
     *
     * @param int $planSurveyId 标签id
     * @return array 【1】 标签列表
     */
    public function getSurveyTags($planSurveyId);
}