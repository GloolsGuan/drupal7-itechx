<?php


namespace service\plan\interfaces;

/**
 * Interface Permission
 * @design yangzy 20161122
 * @package service\plan\interfaces
 */
interface Permission
{
    /**
     * 新增权限
     *
     * @param string $title 权限名称
     * @param string $path url地址
     * @param string $desc 权限描述
     * @return array 新增的权限数据 统一格式
     */
    public function create($title, $path, $desc = '');

    /**
     * 删除权限
     *
     * @param int $id 权限id
     * @return array 统一格式
     */
    public function delete($id);

    /**
     * 更新权限信息
     *
     * @param int $id 权限id
     * @param string $title 权限名称
     * @param string $desc 权限描述
     * @return array 统一格式
     */
    public function update($id, $title, $path, $desc = '');

    /**
     * 获取权限信息
     *
     * @param int $id
     * @return array 统一格式
     */
    public function getPermission($id);

    /**
     * 获取权限列表
     *
     * @return array 统一格式
     */
    public function getPermissions();
}