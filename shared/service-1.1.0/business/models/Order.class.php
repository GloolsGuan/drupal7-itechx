<?php
/**
 * 订单模型定义
 */

namespace service\business\models;

use \service\base\db\SimpleAR;
use \service\business\models\OrderGoods;

class Order extends SimpleAR
{
    
    /**
     * 订单状态：-1：取消，0：待支付，1：已支付，2：待审核，3：审核通过，4：审核未通过
     */
    const STATUS_CANCELED = -1;
    const STATUS_UNPAYED = 0;
    const STATUS_PAYED = 1;
    const STATUS_UNAUDITED = 2;
    const STATUS_AUDITED = 3;
    const STATUS_FAILAUDITED = 4;
    
    public static function tableName()
    {
        return 'biz_order';
    }
    
    /**
     * 获取订单列表
     *
     * @param array $map 查询条件，格式参照yii2 where()方法
     * @param int $offset 数据偏移量
     * @param int $limit 条数限制
     * @return array 订单列表数据 统一格式
     * @author drce 20161123
     */
    public static function getOrders(array $map = [], $offset = 0, $limit = 20)
    {
        $query = static::find()->from(['order' => static::tableName()])
        ->select([
            'order.*'
        ])
        ->where($map);
    
        $orders = $query->offset($offset)->limit($limit)->orderBy('order.id DESC')->asArray()->all();
    
        return [$orders, $query->count()];
    }







    /**
     * 统计订单数
     *
     * @param array $map 约束条件，格式参照yii2 where()方法
     * @return array 订单数 统一格式
     * @author drce 20161123
     */
    public static function countOrders(array $map = [])
    {
        $query = static::find()->from(['order' => static::tableName()])
        ->select([
            'order.*'
        ])
        ->where($map);
    
        return $query->count();
    }

    /**
     * 审核订单，标记订单状态
     *
     * @param int $orderId 订单id
     * @param bool $approved 是否通过审核
     * @return array 统一格式|string error 错误状态
     * @author drce 20161123
     */
    public static function auditOrder($orderId, $approved = true)
    {
        if (NULL === ($order = static::find()->where(['id' => $orderId])->one())) return 'ORDER_LOSE';
        
        if (static::STATUS_UNAUDITED != $order['status']) return 'STATUS_ERROR';
        
        $approved ? //审核通过
            $attributes = ['status' => static::STATUS_AUDITED,'review_time' => date("Y-m-d H:i:s")]
            : //审核失败
            $attributes = ['status' => static::STATUS_FAILAUDITED,'review_time' => date("Y-m-d H:i:s")]
        ;
    
        $order->setAttributes($attributes);
    
        if (false === $order->save()) return 'SAVE_ERROR';
    
        return $order->getAttributes();
    }
    
    /**
     *  获取所有订单
     * @param array $map
     * @param number $offset
     * @param number $limit
     * @param string $order
     */
    public static function getAllOrders(array $map=[], $offset = 0, $limit = 20,$order=''){
    	$query = static::find()->from(['order' => static::tableName()])
    	->select([
    			'order.*',
    			'g.*',
    	])
    	->where($map)
    	->join('LEFT JOIN',OrderGoods::tableName() .' AS g', 'order.id = g.order_id');
    	$orders = $query->offset($offset)->limit($limit)->orderBy('order.id DESC')->asArray()->all();
    	
    	return [$orders, $query->count()];
    }



}