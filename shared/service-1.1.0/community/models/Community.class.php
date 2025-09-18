<?php
namespace service\community\models;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Community extends BaseActiveRecord{
    
    public function __construct($config = array()) {
        parent::__construct($config);
    }
    
    public function rules(){
        return [
            [['title', 'master_title', 'master_code', 'master_ext_id', 'founder_uc_code'], 'required'],
            [['master_code', 'founder_uc_code', 'operator_uc_code'], 'string', 'length'=>64],
            [['title', 'master_title'], 'string', 'min'=>2, 'max'=>255],
            ['master_ext_id', 'number'],
            ['logo', 'file', 'extensions'=>['png','jpg','jpeg'], 'maxSize'=>'2M'],
            ['intro', 'string']
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
        return '{{%community}}';
    }
}