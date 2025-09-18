<?php
namespace service\base\db;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * 
 * $db = $this->get('db');
 *  
 *
 */


use service\base\Base as Base;
use Yii;

class ARecord extends \yii\db\ActiveRecord
{
    //-- Master PDO --
    protected static $mpdo = null;

    //-- Slaver PDO --
    protected static $spdo = null;


    public function __construct($config=[]){
        parent::__construct($config);

        self::$mpdo = self::getDb()->getMasterPdo();
        self::$mpdo->query('set names utf8;');
    }


    public static function getDb()
    {
        return Yii::$app->getDb();
    }


    public function _getFields(){
        $attributes = $this->attributes();

        $fields = array();

        foreach($attributes as $at) {
            $fields[$at] = $this->getAttribute($at);
        }

        return $fields;
    }

    /**
     *
     * Usage sample:
     *     $re = $model_community->insert();
     *     if (null==$re && $model_community->hasErrors('db.insert')) {
     *         \GtoolsDebug::testLog(__METHOD__, $model_community->getErrors('db.insert'));
     *         return $this->buildResponse('failed', 501, $model_community->getErrors('db.insert'));
     *     }
     *
     */
    public function insert($runValidation = true, $attributes = null){
        $this->validate();
        if ($this->hasErrors()){
            return false;
        }
        try{
             $re = parent::insert($runValidation, $attributes);
             if (true==$re) {
                 $pk = $this->primaryKey();
                 if(!empty($pk)){
                     $pk = array_shift($pk);
                     return $this->find()->where([$pk=>$this->$pk])->asArray()->one();
                 }
                 return $this->find()->where(['id'=>$this->id])->asArray()->one();
             }
             return false;
        }catch(\Exception $e) {
            \GtoolsDebug::testLog(__METHOD__, $e->getMessage());
            $this->addError('db.insert', $e->getMessage());
            return false;
        }
    }


    public function setAttributes($fields, $safeOnly = true){
        foreach($fields as $key=>$value) {
            if (!$this->hasAttribute($key)) {
                continue;
            }
            $this->setAttribute($key, $value);
        }

        return $this;
    }


    public function rebuildById($raw_records){
        $records = array();
        foreach($raw_records as $k=>$v) {
            $records[$v['id']] = $v;
        }
        return $records;
    }
}