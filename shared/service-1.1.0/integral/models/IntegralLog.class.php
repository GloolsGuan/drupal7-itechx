<?php
namespace service\integral\models;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class IntegralLog extends \service\base\db\ARecord{
    
    
    public function rules(){
        return [
            [['integral_id', 'master_ext_id', 'uc_user_code', 'integral_type', 'value'], 'required'],
            [['uc_user_code'], 'string', 'length'=>64],
            [['integral_type_title'], 'string', 'min'=>0, 'max'=>255],
            [['integral_type'], 'string', 'min'=>0, 'max'=>100],
            [['value'], 'number']
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
    
    
    public static function tableName(){
        return '{{%integral_log}}';
    }
}