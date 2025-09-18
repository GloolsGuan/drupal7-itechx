<?php
namespace service\community\models;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use \service\base\Base;

class BaseActiveRecord extends \service\base\db\ARecord{
    
    public static function getDb(){
        return Base::getCom('db_community');
    }
}