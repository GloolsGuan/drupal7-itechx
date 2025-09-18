<?php
/**
 * 订单商品模型定义
 */

namespace service\business\models;

use \service\base\db\SimpleAR;
use \service\base\ApiClient as ApiClient;


class OrderGoods extends SimpleAR
{

    public static function tableName()
    {
        return 'biz_order_goods';
    }
    
    /**
     * 查询订单中的商品
     *
     * @param int $orderId 订单id
     * @return array 商品列表
     * @author drce 20161123
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