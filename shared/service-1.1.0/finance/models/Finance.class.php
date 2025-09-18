<?php


namespace service\finance\models;

use service\base\Error;

use service\base\db\SimpleAR;

use  \service\plan\models\Plan as ModPlan;
use  \service\goods\models\Goods as ModGoods;
class Finance extends SimpleAR
{

    public static function primaryKey()
    {
        return ['id'];
    }

    public static function tableName()
    {
        return 'biz_order';
    }
	public static function getItemByPlanId($plan_id)
    {
	  $where=[];
	  $where['plan_id']=$plan_id;
      return static::find()->where($where)->one();
    }
	public static function getItems(array $where, $offset = -1, $limit = -1,$order='')
    {
        $query = static::find()->from(['f' => static::tableName()])
		 ->select([
                'f.*',
                'p.plan_name as course'
            ])
		->where($where)
        ->join('LEFT JOIN', ModPlan::tableName() . ' AS p', 'f.course_id = p.plan_id');
        if (-1 != $offset) $query->offset($offset);

        if (-1 != $limit) $query->limit($limit);
        return $query->asArray()->all();
    }
    public static function getAllItems(array $where, $offset = -1, $limit = -1,$order=''){
    	$query = static::find()->from(['f' => static::tableName()])
    	->select([
    			'f.*',
    			'p.plan_name as course',
    			'g.*',
    	])
    	->where($where)
    	->join('LEFT JOIN', ModPlan::tableName() . ' AS p', 'f.course_id = p.plan_id')
    	->join('LEFT JOIN',ModGoods::tableName() .' AS g', 'f.id = g.order_id')
    	;
    	if (-1 != $offset) $query->offset($offset);
    	
    	if (-1 != $limit) $query->limit($limit);
    	return $query->asArray()->all();
    }
	
}