<?php


namespace service\plan\interfaces;

/**
 * Interface Role
 *
 * 角色和权限的关联使用pl_rs_role_permission来保存
 *
 * @design yangzy 20161122
 * @package service\plan\interfaces
 */
interface Role
{
    /**
     * 创建角色
     *
     * @param string $name 角色名称
     * @param string $desc 角色描述
     * @return array 新创建的角色数据 统一格式
     */
    public function create($name, $desc = '');

    /**
     * 删除角色
     *
     * @param int $id 角色id
     * @return array 统一格式
     */
    public function delete($id);

    /**
     * 更新角色信息
     *
     * @param int $id 角色id
     * @param string $name 角色名称
     * @param string $desc 角色描述
     * @return array 统一格式
     */
    public function update($id, $name, $desc = '');

    /**
     * 获取角色信息
     *
     * @param int $id
     * @return array 统一格式
     */
    public function getRole($id);

    /**
     * 获取角色列表
     *
     * @return array 统一格式
     */
    public function getRoles();

    /**
     * 获取角色的权限
     *
     * @param int $id 角色id
     * @return array 权限列表 统一格式
     */
    public function getPermissions($id);

    /**
     * 给角色设置权限
     *
     * @param int $id 角色id
     * @param array $permissionIds 权限id组成的数组
     * @return array 统一格式
     */
    public function setPermission($id, array $permissionIds);
}