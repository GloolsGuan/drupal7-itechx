<?php
/**
 * 角色模型定义
 */

namespace service\plan\models;

use \service\base\db\SimpleAR;


class Role extends SimpleAR
{
    
    
    public static function tableName()
    {
        return 'pl_role';
    }
    
    
    
}