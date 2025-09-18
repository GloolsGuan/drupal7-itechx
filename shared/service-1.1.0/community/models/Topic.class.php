<?php
namespace service\community\models;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Topic extends BaseActiveRecord{
    
    public function __construct($config = array()) {
        parent::__construct($config);
    }
    
    public function rules(){
        return [
            [['content', 'group_id'], 'required'],
            [['member_uc_code'], 'string', 'length'=>64],
            [['title'], 'string', 'min'=>0, 'max'=>255],
            ['member_id', 'number'],
        ];
    }
    
    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('UNIX_TIMESTAMP(NOW())'),
            ]
        ];
    }
    
    public static function tableName()
    {
        return '{{%community_topic}}';
    }
}