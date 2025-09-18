<?php
/**
 *
 */

namespace service\plan\interfaces;

/**
 * Interface SurveyTag
 * 调研标签
 * 相关数据表 pl_survey_tag标签表,pl_rs_survey_tag标签与调研关联表
 *
 * @package service\plan\interfaces
 * @design yangzy 20161204
 * @author yangzy 20161213
 */
interface SurveyTag
{
    /**
     * 创建标签
     * @param array $data 数据格式参照标签表
     * @return array 成功时返回新创建标签数据【1】
     */
    public function create(array $data);

    /**
     * 删除标签
     * @param array $ids 标签id组成的数组
     * @return array 成功时返回被删除的标签id【1】
     */
    public function delete(array $ids);

    /**
     * 修改标签属性
     * @param int $id 标签id
     * @param array $data 标签新属性
     * @return array 成功时返回修改后的标签的数据【1】
     */
    public function update($id, array $data);

    /**
     * 根据标签id获取标签详情
     * @param array $ids 标签id组成的数组
     * @return array 返回标签数据列表【1】
     */
    public function getTags(array $ids);

    /**
     *获取标签列表
     * @param array $map 过滤条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return array 返回标签数据列表【1】
     */
    public function listTags(array $map = [], $offset = 0, $limit = 20);

    /**
     * 获取计划所包含的的调研标签列表
     * @param int $planId 计划id
     * @param array $map 过滤条件
     * @param int $offset
     * @param int $limit
     * @return array 返回标签数据列表【1】
     */
    public function listPlanTags($planId, array $map = [], $offset = -1, $limit = -1);
}