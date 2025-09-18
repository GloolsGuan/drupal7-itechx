<?php
namespace service\plan;

use \service\base\Module;
use \service\plan\models\Role as ModRole;
use \service\plan\interfaces\Role as RoleInterface;

class Role extends Module implements RoleInterface
{
    /**
     * 创建角色
     *
     * @param string $name 角色名称
     * @param string $desc 角色描述
     * @return array 新创建的角色数据 统一格式
     */
    public function create($name, $desc = '')
    {
        if (empty($name)) return $this->buildResponse('error', 400, '$name cannot be empty');

        $model = new ModRole();
        $model->setAttributes(['name' => $name, 'desc' => $desc]);
        
        if (false === $model->save()){
            return $this->buildResponse('failed', 400, 'failed to add Role resource');
        }else{
            return $this->buildResponse('success', 201, $model->getAttributes());
        }
    }

    /**
     * 删除角色
     *
     * @param int $id 角色id
     * @return array 统一格式
     */
    public function delete($id)
    {
        
    }

    /**
     * 更新角色信息
     *
     * @param int $id 角色id
     * @param string $name 角色名称
     * @param string $desc 角色描述
     * @return array 统一格式
     */
    public function update($id, $name, $desc = '')
    {
        if (empty($id)) return $this->buildResponse('error', 400, '$id cannot be empty');
        if (empty($name)) return $this->buildResponse('error', 400, '$name cannot be empty');
        
        if (NULL === ($role = ModRole::find()->where(['id' => $id])->one())) return $this->buildResponse('failed', 400, 'catalog does not exist');
        
        $role->setAttributes(['name' => $name, 'desc' => $desc]);
        
        if (false === $role->save()) return $role->buildResponse('failed', 400, 'failed to edit Role resource');
        
        return true;
    }

    /**
     * 获取角色信息
     *
     * @param int $id
     * @return array 统一格式
     */
    public function getRole($id)
    {
        
    }

    /**
     * 获取角色列表
     *
     * @return array 统一格式
     */
    public function getRoles()
    {
        $query = ModRole::find()->from(['role' => ModRole::tableName()])
        ->select([
            'role.*'
        ]);
        
        $roles = $query->orderBy('role.id DESC')->asArray()->all();
        
        $status = empty($roles) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $roles);
    }

    /**
     * 获取角色的权限
     *
     * @param int $id 角色id
     * @return array 权限列表 统一格式
     */
    public function getPermissions($id)
    {
        
    }

    /**
     * 给角色设置权限
     *
     * @param int $id 角色id
     * @param array $permissionIds 权限id组成的数组
     * @return array 统一格式
     */
    public function setPermission($id, array $permissionIds)
    {
        
    }
}

?>