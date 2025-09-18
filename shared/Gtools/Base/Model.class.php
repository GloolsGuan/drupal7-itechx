<?php
namespace Gtools\Base;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Model extends \yii\db\ActiveRecord{
 
    //-- Master PDO --
    protected static $mpdo = null;

    //-- Slaver PDO --
    protected static $spdo = null;
    


    public function __construct($config=[]){
        parent::__construct($config);

        self::$mpdo = self::getDb()->getMasterPdo();
        self::$mpdo->query('set names utf8;');
        
        //\Gtools\Debug::testLog(__METHOD__, $this, __LINE__);
    }
    
    public function behaviors(){
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                //'value' => new \yii\db\Expression('UNIX_TIMESTAMP(NOW())'),
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }
    
    
    public static function getDb()
    {
        $db_conf = \App\Config::get('yii_coms/db');
        $com_definition = $db_conf;
        $com_definition['class'] = 'yii\db\Connection';
        //return new \yii\db\Connection($db_conf);
        return \Yii::createObject($com_definition);
    }
    
    
    public function getEntityData(){
        ;
    }
    
    
    
}