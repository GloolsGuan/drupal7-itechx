<?php
/**
 *
 */

namespace service\user\interfaces;

/**
 * 用户中心ucenter标签服务功能
 *
 * @package service\user\interfaces
 * @design guanxm 20161123
 * @author guanxm 20161123
 */
interface Tag
{
    /**
     * 获取一个标签的详细信息
     *
     * @param string $tag_name 标签名称
     * @return array 返回标签的数据 统一格式
     */
    public function load($tag_name);
    
    /**
     * Retrieving children list
     * 
     * @param string $tag_name Tag name
     * @return array Tag list.
     */
    public function loadChildren($tag_name);
    
    
    /**
     * Create an new tag
     * 
     * @param type $tag_data
     *   - "name" string Tag name
     *   - "intro"  string Description for the tag
     *   - "parent_id" numeric Parent tag id.
     * 
     * @param string(64) $user_code The operator's user code, Generally it is 64bit string.
     * 
     * @return string Created tag entity data
     */
    public function create($tag_data, $operator_code);
    
    
    /**
     * Update an exists tag
     * 
     * @param numeric $tag_id
     * @param array $tag_data
     * @param string(64) $user_code
     * 
     * @return array updated result
     */
    public function update($tag_id, $tag_data, $operator_code);
    
    
    /**
     * Remove an exists tag
     * 
     * @param numeric $tag_id
     * @param string(64) $user_code
     * 
     * @return array Remove result
     */
    public function remove($tag_id, $operator_code);
    
    
    /**
     * Load members by tag ID
     * 
     * @param numeric $tag_id
     * 
     * @return array Result
     */
    public function loadMembers($tag_id);
    
    
    /**
     * Load members by tag name
     * 
     * @param string $tag_name
     * 
     * @return array Result
     */
    public function loadMembersByTagName($tag_name);
    
    
    /**
     * Mark members with tag
     * 
     * @param type $tag_id
     * @param type $members
     * @param type $user_code
     * 
     * @return array Result
     */
    public function addMembers($tag_id, $members, $operator_code);
    
    
    /**
     * Remove tag mark for an member
     * 
     * @param numeric $tag_id
     * @param string(64) $member_code
     * @param string(64) $user_code Operator's user code.
     */
    public function removeMember($tag_id, $member_code, $operator_code);
    
}