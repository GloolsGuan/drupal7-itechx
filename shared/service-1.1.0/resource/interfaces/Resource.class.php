<?php
/**
 *
 */

namespace service\resource\interfaces;

/**
 * Interface Resource
 * 资料库接口
 * 相关数据表resource, resource_tag, resources
 *
 * @package service\resource\interfaces
 * @design yangzy 20161211
 * @author yangzy 20161211
 */
interface Resource
{
    /**
     * 查询资源详情
     *
     * @param int $id 资源id
     * @return array 【1】 资源数据，格式参照resource表字段
     */
    public function getResourceById($id);

    /**
     * 查询指定的资源
     * @param array $ids 资源id组成的数组
     * @return array 【1】 资源数据组成的数组，格式参照resource表字段
     */
    public function getResourceByIds(array $ids);

    /**
     * 删除资料
     *
     * @param array $ids 资料id组成的数组
     * @return array 成功时返回被删除的资料id组成的数组
     */
    public function deleteResource(array $ids);
}