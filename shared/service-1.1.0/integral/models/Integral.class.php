<?php
namespace service\integral\models;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Integral extends \service\base\db\ARecord{
    
    public function rules(){
        return [
            [['title', 'master_title', 'master_code'], 'required'],
            [['title'], 'string', 'min'=>0, 'max'=>255],
            [['master_title'], 'string', 'min'=>0, 'max'=>255],
            [['master_code'], 'string', 'length'=>64]
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
        return '{{%integral}}';
    }
}