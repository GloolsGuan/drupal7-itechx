<?php


namespace service\application\models;

class Application extends \service\base\db\ARecord
{

    static public function tableName()
    {
        return 'application';
    }

    static public function primaryKey()
    {
        return ['id'];
    }
	
    public static function getApplication($id)
    {
        return static::findOne($id)->toArray();
    }


    
    /**
     * @param $offset
     * @param $limit
     * @return array|\service\base\db\ActiveRecord[]
     */
    public static function getAppList($offset, $limit)
    {
        return static::find()->offset($offset)
            ->limit($limit)
            ->orderBy('id DESC')
            ->asArray()
            ->all();
    }

    public static function getHomepage($app_id)
    {
        $where['id'] = $app_id;
        return static::find()->where($where)->one()->toArray();
    }

    public static function saveApp($id, array $data)
    {
        if (false != ($app = static::findOne($id))) {
        } else {
            $app = new static();
        }
        $app->setAttributes($data);
        return $app->save();
    }
    
}