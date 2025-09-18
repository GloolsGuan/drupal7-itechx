<?php


namespace service\plan\interfaces;

/**
 * Interface Group
 * @package service\plan\interfaces
 * @design yangzy 20161122
 * @author shenf 20161125
 */
interface Group
{
    /**
     * 将学员加入小组，学员不能同时存在于两个小组
     *
     * @param int $groupId 组id
     * @param int $participantId 学员id
     * @return array 成功时返回true
     */
    public function addMember($groupId, $participantId);

    /**
     * 设置学习小组的组长
     *
     * @param int $planId 计划id
     * @param int $groupId 小组id
     * @param int $participantId 学员id
     * @return  boolean|array 成功时返回true
     */
    public function setGroupLeader($planId, $groupId, $participantId);
}