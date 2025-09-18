<?php
/**
 * laughstorm
 * 保证【模型层】干净，业务逻辑全部写到【服务层】
 */
namespace service\article\models;

class Article extends \service\base\db\ARecord{
    
    const STATUS_DELETED = -1; //删除状态
    const STATUS_NORMAL = 1; //正常状态

    public static function tableName(){
        return 'article';
    }

    public function rules(){
        return [
            [['title','creator'], 'required'],
            ['status', 'default', 'value' => self::STATUS_NORMAL],
            [['create_time', 'update_time'], 'default', 'value' => date('Y-m-d H:i:s', time())]
        ];
    }
    
    public function attributeLabels(){
        return [
            'id' => 'Id',
            'title' => '标题',
            'creator' => '创建者',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'status' => '状态',
        ];
    }
}