<?php
/**
 *
 */

namespace service\plan\interfaces;

/**
 * Interface Enroll
 *
 * @package service\plan\interfaces
 * @design liurw 20161116
 */
interface Enroll
{
    /**
     * 添加
     * @param array $data
     * @return array|bool
     */
    public function create(array $data);

    /**
     * 添加/更新详情
     * @param array $data
     * @return array|bool
     */
    public function saveEnroll(array $data);

    public function getItemByPlanId($plan_id);

    public function getPlanByAppid($appid);

    public function getCourse($where, $offset = 0, $limit = 20, $order = '');

    public function listPlans($name = '', $sort = '', $page = 1, $page_size = 20);

    public function getCourseByIds($ids = []);
}