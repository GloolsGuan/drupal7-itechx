<?php
/**
 *
 */

namespace service\plan\interfaces;

/**
 * Interface Log
 * 相关数据表lg_action,lg_log
 *
 * @package service\plan\interfaces
 * @design yangzy 20161129
 * @author shenf 20161130
 */
interface Log
{
    /**
     * 写入日志
     * @param array $log 日志内容 格式参考lg_log数据表
     * @return 成功时返回true
     */
    public function record(array $log);

    /**
     * 查询日志
     *
     * @param array $map 查询条件，格式参照yii2 where方法参数
     * @return array 日志 [1]
     */
    public function history(array $map);
}