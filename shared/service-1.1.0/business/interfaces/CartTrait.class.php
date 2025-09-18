<?php
/**
 *
 */

namespace service\business\interfaces;

/**
 * Class CartTrait
 *
 * @package service\plan\interfaces
 * @design yangzy 20161123
 * @author yangzy 20161123
 */
trait CartTrait
{
    /**
     * 购物车的所有者
     *
     * @var string 用户的unique_code
     */
    public $userId;

    /**
     * 设置购物车所有者
     *
     * @param string $userId 用户unique_code
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
}