<?php
/**
 * 退单商品模型定义
 */

namespace service\business\models;

use \service\base\db\SimpleAR;


class RefundGoods extends SimpleAR
{
    
    public static function tableName()
    {
        return 'biz_refund_goods';
    }
    
    /**
     * 查询订单中的商品
     *
     * @param int $orderId 订单id
     * @return array 商品列表
     * @author drce 20161201
     */
    public static function getGoods(array $map = [])
    {
        $query = static::find()->from(['order_goods' => static::tableName()])
        ->select([
            'order_goods.*'
        ])
        ->where($map)
        ->orderBy('order_goods.id DESC');
        
        $order_goods = $query->asArray()->all();
        
        return $order_goods;
    }
    
}