<?php
namespace service\community\models;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Member extends BaseActiveRecord{
    
    public function __construct($config = array()) {
        parent::__construct($config);
    }
    
    
    public function rules(){
        return [
            [['uc_user_code', 'role_id', 'participation_type'], 'required'],
            [['uc_user_code'], 'string', 'length'=>64],
            [['participation_type', 'honor_title', 'nickname'], 'string', 'min'=>1, 'max'=>255],
            ['role_id', 'number']
        ];
    }
    
    
    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UNIX_TIMESTAMP(NOW())'),
            ],
        ];
    }
    
    public static function tableName()
    {
        return '{{%community_group_member}}';
    }
}