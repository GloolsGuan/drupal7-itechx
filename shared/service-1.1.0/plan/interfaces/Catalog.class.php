<?php
/**
 *  计划培训体系模块
 */

namespace service\plan\interfaces;

/**
 * 培训目录操作接口
 *
 * 目录与课程使用RsPlanCatalog进行关联
 *
 * 说明：
 * 1、通过表字段xpath查询子孙级目录的sql，设$path = 'a/b/c';WHERE xpath > $path.'/' AND $path < $path.'0'
 *
 * 方法的统一返回值格式（统一格式） array ['status'=>$status,'code'=>$code,'data'=>$data]
 * $status:状态
 * $code:状态码(200：成功，无返回数据，201：成功，有返回数据,400：错误)
 * $data:返回数据
 *
 * @package service\plan
 * @design yangzy 20161121
 * @author shenf 20161123
 */
interface Catalog
{

    /**
     * 添加新目录
     *
     * @param string $name 目录名称 不能为空
     * @param int $pid 父目录ID，为0时表示添加到根目录
     * @return array 成功时返回新添加的目录数据，使用统一返回值格式
     * @design yangzy 20161120
     */
    public function addItem($name, $pid = 0);

    /**
     * 删除指定的目录（将目录的状态标记为删除）
     *
     * @param int $id 目录的id
     * @return boolean|array 成功时返回true
     * @design yangzy 20161120
     */
    public function deleteItem($id);

    /**
     *修改目录属性
     *
     * @param $id 要修改的目录的id
     * @param array $attributes 目录新的属性，不能为空
     * @return boolean|array 成功时返回true
     * @design yangzy 20161120
     */
    public function editItem($id, array $attributes = []);

    /**
     * 查询目录的属性数据
     *
     * @param int $id 目录的id
     * @return array 统一格式
     * @design yangzy 20161120
     */
    public function getItem($id);

    /**
     * 查询子（孙）目录（树型层级结构）
     *
     * @param int $pid 父级目录的id
     * @param bool $children 为true时，返回子孙目录；为false时，只返回子目录
     * @return array 统一格式
     * @design yangzy 20161120
     */
    public function getSubitems($pid, $children = false);

    /**
     * 给目录添加课程
     *
     * @param int $catalogId 目录id
     * @param int|array $planId 课程id或课程id组成的数组
     * @return array 成功时返回true
     * @design yangzy 20161120
     */
    public function addPlan($catalogId, $planId);

    /**
     * 从目录中移除课程
     *
     * @param $catalogId 目录id
     * @param int|array $planIds 课程id或课程id组成的数组
     * @return 成功时返回true
     * @design yangzy 20161120
     */
    public function removePlan($catalogId, $planIds);

    /**
     * 列举目录下的课程
     *
     * @param int $catalogId 课程id
     * @return array 统一格式
     * @design yangzy 20161120
     */
    public function listCatalogPlan($catalogId);

    /**
     * 获取课程计划所关联的课程目录
     *
     * @param int $planId 计划id
     * @return array 返回课程目录数据 [1]
     * @design yangzy 20161206
     * @author shenf 20161206
     */
    public function listPlanCatalogs($planId);

    /**
     * 移除课程计划所关联的课程目录
     *
     * @param int $planId 课程计划id
     * @param int|array $catalogIds 课程目录id或课程目录id组成的数组
     * @return array 成功时返回true
     * @design yangzy 20161206
     * @author shenf 20161206
     */
    public function removePlanCatalogs($planId, $catalogIds);
}