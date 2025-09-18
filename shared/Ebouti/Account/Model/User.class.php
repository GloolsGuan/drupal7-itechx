<?php
namespace Ebouti\Account\Model;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class User extends \Gtools\Base\Model{
    
    
    
    protected $raw_entity = [];

    
    public function create(){
        
    }
    
    
    public function getEntityData(){
        return $this->getAttributes(null, ['openid']);
    }
    
    public static function tableName(){
        return '{{%eb_user}}';
    }
    
    
}
