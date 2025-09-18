<?php
namespace service\community\models;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Like extends BaseActiveRecord{
    
    public function __construct($config = array()) {
        parent::__construct($config);
    }
    
    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                //'value' => new Expression('UNIX_TIMESTAMP(NOW())'),
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    
    
    public static function tableName()
    {
        return '{{%community_like}}';
    }
}