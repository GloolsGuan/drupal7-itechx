<?php
/**
 *
 */

namespace service\plan\interfaces;

/**
 * Interface Share
 * 分享接口
 * 相关数据表pl_share
 *
 * @package service\plan\interfaces
 * @design yangzy 20161208
 * @author yangzy 20161209
 */
interface Share
{
    /**
     * 获取分享列表
     *
     * @param array $search 过滤条件，格式参照yii2 where()参数
     * @param int $offset
     * @param int $limit
     * @return array 成功时返回分享列表【1】[
     * [
     * 'id' => 1,//分享id
     * 'userName' => '小王',//用户名
     * 'phone' => '2343456',//手机号
     * 'corporation' => '岱恩教育',//机构
     * 'planId' => 2,//培训id
     * 'planTitle' => '培训',//培训名称
     * 'beginTime' => '2016-12-10 09:00:00',//培训开始时间
     * 'endTime' => '2016-12-10 09:00:00',//培训结束时间
     * 'title' => '实现安全.doc',//分享的名称
     * 'fileId'=>22,//文件id
     * ]
     * ]
     *
     *
     */
    public function search(array $search = [], $offset = -1, $limit = -1);

    /**
     * 获取指定的分享数据列表
     *
     * @param array $ids 分享id组成的数组
     * @return array【1】 分享数据组成的列表
     * [
     * [
     * 'id' => 1,//分享id
     * 'userName' => '小王',//用户名
     * 'phone' => '2343456',//手机号
     * 'corporation' => '岱恩教育',//机构
     * 'planId' => 2,//培训id
     * 'planTitle' => '培训',//培训名称
     * 'beginTime' => '2016-12-10 09:00:00',//培训开始时间
     * 'endTime' => '2016-12-10 09:00:00',//培训结束时间
     * 'title' => '实现安全.doc',//分享的名称
     * 'fileId'=>22,//文件id
     * ]
     * ]
     */
    public function getShares(array $ids);

    /**
     * 创建一个分享
     *
     * @param array $shareData 数据参照pl_share表
     * @return array【1】成功时返回新创建分享的数据
     * [
     * 'id' => 1,//分享id
     * 'userName' => '小王',//用户名
     * 'phone' => '2343456',//手机号
     * 'corporation' => '岱恩教育',//机构
     * 'planId' => 2,//培训id
     * 'planTitle' => '培训',//培训名称
     * 'beginTime' => '2016-12-10 09:00:00',//培训开始时间
     * 'endTime' => '2016-12-10 09:00:00',//培训结束时间
     * 'title' => '实现安全.doc',//分享的名称
     * 'fileId'=>22,//文件id
     * ]
     */
    public function createShare(array $shareData);

    /**
     * 删除分享
     *
     * @param array $ids 分享id组成的数组
     * @return array 成功时返回删除分享的id组成的数组
     */
    public function deleteShares(array $ids);
}