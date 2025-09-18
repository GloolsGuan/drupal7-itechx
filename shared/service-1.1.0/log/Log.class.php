<?php
/**
 *
 */

namespace service\log;

use com\helpers\ArrayHelper;
use service\base\Module;
use service\log\interfaces\Log as LogInterface;
use service\log\models\Action;
use service\log\models\Log as LogModel;

/**
 * Class Log
 * @package service\plan
 * @author yangzy 20161213
 */
class Log extends Module implements LogInterface
{
    /**
     * 写入日志，取出日志action对应的模板记录日志内容
     *
     * @param array $log 日志内容 格式参考lg_log数据表
     * @return 成功时返回true
     */
    public function record(array $log)
    {
        static $cache = [];

        $actionId = ArrayHelper::getValue($log, 'action_id', '');
        if (empty($actionId))
            return $this->buildResponse('error', 400, 'no action id');

        if (isset($cache[$actionId])) {
            $action = $cache[$actionId];
        } else {
            $action = Action::getItemById($actionId);
            if (empty($action)) {
                $cache[$actionId] = [];
                return $this->buildResponse('error', 400, 'action not found');
            }
            $cache[$actionId] = $action;
        }
        $action = $cache[$actionId];
        if (empty($action))
            return $this->buildResponse('error', 400, 'action not found');

        //TODO 处理不同的action模板
        $log['text'] = str_replace(['USER'], [$log['unique_code']], $action['template']);

        if (false === $item = LogModel::createItem($log))
            return $this->buildResponse('failed', 400, 'failed to insert log');
        return $this->buildResponse('success', 201, $item->getAttributes());
    }

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
    public function insert($module, $userId, $actionId, $targetId, array $log = [])
    {
        $log['unique_code'] = $userId;
        $log['action_id'] = $actionId;
        return $this->record($log);
    }

    /**
     * @inheritDoc
     */
    public function history(array $map = [], $offset = -1, $limit = -1, $order = 'create_time desc')
    {
        if (false === $items = LogModel::getItems($map, $offset, $limit, $order))
            return $this->buildResponse('failed', 400, 'failed to list logs');
        return $this->buildResponse('success', 201, $items);
    }
}