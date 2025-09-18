<?php
/**
 * 日志模块
 * 日志有所属模块,行为者,行为类型和行为目标等概念
 */

namespace service\log\interfaces;

/**
 * Interface Log
 * 相关数据表lg_action,lg_log
 *
 * @package service\log\interfaces
 * @design yangzy 20161129
 */
interface Log
{
    /**
     * 写入日志，取出日志action对应的模板记录日志内容
     *
     * @param array $log 日志内容 格式参考lg_log数据表
     * @return 成功时返回true
     */
    public function record(array $log);

    /**
     * 写入日志
     *
     * @param int $module 模块id
     * @param string $userId 用户id
     * @param int $actionId 行为id
     * @param int $targetId 目标id
     * @param array $log 日志内容
     * @return array 成功时返回true
     */
    public function insert($module, $userId, $actionId, $targetId, array $log = []);

    /**
     * 查询日志
     *
     * @param array $map 查询条件，格式参照yii2 where方法参数
     * @return array 日志 [1]
     */
    public function history(array $map = [], $offset = -1, $limit = -1, $order = 'create_time desc');
}