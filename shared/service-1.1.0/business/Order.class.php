<?php

namespace service\business;

use \service\base\Module;
use \service\business\models\Order as ModOrder;
use \service\business\models\OrderGoods as ModOrderGoods;
use \service\business\interfaces\Order as OrderInterface;
use \service\business\interfaces\OrderStatus as OrderStatusInterface;

class Order extends Module implements OrderInterface,OrderStatusInterface
{

    /**
     * 创建订单
     *
     * @param array $order 订单详情，数据字段参照数据表biz_order
     * @return array 返回新创建的订单数据，统一数据格式
     * @author shenf 20161123
     */
    public function createOrder(array $order = [])
    {
        
        $can_not_empty = array('paymode','unique_code');
        
        foreach ($can_not_empty as $key => $val){
            if ( !isset($order[$val]) || empty($order[$val]) ) return $this->buildResponse('error', 400, $val.' cannot be empty');
        }
        
        if( !isset($order['amount']) ) return $this->buildResponse('error', 400, 'amount cannot be empty');
        
        list($t1, $t2) = explode('.', microtime(true));
        $order['order_no'] = date("ymdHis").$t2.rand(10,99);
        $order['status'] = ModOrder::STATUS_UNPAYED;
        $order['create_time'] = date("Y-m-d H:i:s");
        
        $model = new ModOrder();
        $model->setAttributes($order);
        
        if (false === $model->save())
            return $this->buildResponse('failed', 400, 'failed to add Order resource');
        else
            return $this->buildResponse('success', 201, $model->getAttributes());
    }
    
    /**
     * 查询订单详情
     *
     * @param int $id 订单id
     * @return array 订单详情 统一格式
     * @author shenf 20161123
     */
    public function getOrder($id)
    {
        if (empty($id)) return $this->buildResponse('error', 400, '$id cannot be empty');
        
        if (NULL === ($order = ModOrder::find()->where(['id' => $id])->one())) return $this->buildResponse('failed', 400, 'order does not exist');
        
        $status = empty($rows = $order->getAttributes()) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $rows);
    }

    /**
     * 获取订单列表
     *
     * @param array $map 查询条件，格式参照yii2 where()方法
     * @param int $offset 数据偏移量
     * @param int $limit 条数限制
     * @return array 订单列表数据 统一格式
     * @author shenf 20161123
     */
    public function getOrders(array $map = [], $offset = 0, $limit = 20)
    {
        if (!is_array($map)) return $this->buildResponse('error', 400, '$map must be array');
        
        $orders = ModOrder::getOrders($map, $offset, $limit);
        
        $status = empty($orders) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $orders);
        
    }

    /**
     * 统计订单数
     *
     * @param array $map 约束条件，格式参照yii2 where()方法
     * @return array 订单数 统一格式
     * @author shenf 20161123
     */
    public function countOrders(array $map = [])
    {
        if (!is_array($map)) return $this->buildResponse('error', 400, '$map must be array');
        
        $orders_num = ModOrder::countOrders($map);
        
        $status = empty($orders_num) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $orders_num);
    }

    /**
     * 向订单中添加商品
     *
     * @param int $order_id 订单id
     * @param array $good 商品详情 数据格式参照biz_order_goods表
     * @return 返回添加后的商品数据，统一数据格式
     * @author shenf 20161123
     */
    public function addGood($order_id, array $good = [])
    {
        if (empty($order_id)) return $this->buildResponse('error', 400, '$order_id cannot be empty');
        if (!is_array($good)) return $this->buildResponse('error', 400, '$good must be array');
        
        $can_not_empty = array('good_number','course_id');
        
        foreach ($can_not_empty as $key => $val){
            if ( !isset($good[$val]) || empty($good[$val]) ) return $this->buildResponse('error', 400, '$'.$val.' cannot be empty');
        }
        
        if( !isset($good['good_price']) ) return $this->buildResponse('error', 400, '$good_price cannot be empty');
        
        if (NULL === (ModOrder::find()->select(['id'])->where(['id' => $order_id])->one())) return $this->buildResponse('failed', 400, 'order does not exist');
        
        $model = new ModOrderGoods();
        $good['order_id'] = $order_id;
        $model->setAttributes($good);

        if (false === $model->save())
            return $this->buildResponse('failed', 400, 'failed to add OrderGood resource');
        else
            return $this->buildResponse('success', 201, $model->getAttributes());
    }
    
    /**
     * 从订单中删除指定的商品
     *
     * @param int $orderId 订单id
     * @param int $orderGoodId 订单商品id
     * @return boolean|array 成功返回true [1]
     * @author shenf 20161123
     */
    public function deleteGood($orderId, $orderGoodId)
    {
        if (empty($orderId)) return $this->buildResponse('error', 400, '$orderId cannot be empty');
        if (empty($orderGoodId)) return $this->buildResponse('error', 400, '$orderGoodId must be array');
        
        if (NULL === (ModOrder::find()->select(['id'])->where(['id' => $orderId])->one())) return $this->buildResponse('failed', 400, 'order does not exist');
        
        $deleteParm = array();
        
        if (is_array($orderGoodId)){
            $deleteParm = [ ['and','order_id = :orderId',['in', 'id', $orderGoodId]], [':orderId' => $orderId] ];
        }else{
            $deleteParm = [ ['and','order_id = :orderId','id = :orderGoodId'], [':orderId' => $orderId,':orderGoodId' => $orderGoodId] ];
        }
        
        if (FALSE === ModOrderGoods::deleteAll($deleteParm[0],$deleteParm[1])) return $this->buildResponse('failed', 400, 'failed to remove OrderGoods resource');
        
        return true;
    }

    /**
     * 查询订单中的商品
     *
     * @param int $orderId 订单id
     * @return array 商品列表
     * @author shenf 20161123
     */
    public function getGoods($orderId)
    {
        if (empty($orderId)) return $this->buildResponse('error', 400, '$orderId cannot be empty');
        
        $order_goods = ModOrderGoods::getGoods(['order_id'=>$orderId]);
        
        $status = empty($order_goods) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $order_goods);
    }

    public function getGoodsByOrderId($orderId)
    {
        if (empty($orderId)) return $this->buildResponse('error', 400, '$orderId cannot be empty');

        $order_goods = ModOrderGoods::getGoodsByOrderId($orderId);

        $status = empty($order_goods) ? 200 : 201;

        return $this->buildResponse('success', $status, $order_goods);
    }



    /**
     * 审核订单，标记订单状态
     *
     * @param int $orderId 订单id
     * @param bool $approved 是否通过审核
     * @return array 统一格式
     * @author shenf 20161123
     */
    public function auditOrder($orderId, $approved = true)
    {
        if (empty($orderId)) return $this->buildResponse('error', 400, '$orderId cannot be empty');
        
        $order = ModOrder::auditOrder($orderId);
        
        if (!is_array($order)){
            if ('ORDER_LOSE' == $order) return $this->buildResponse('error', 400, 'order does not exist');
            if ('STATUS_ERROR' == $order) return $this->buildResponse('error', 400, 'order status error');
            if ('SAVE_ERROR' == $order) return $order->buildResponse('failed', 400, 'failed to save order resource');
        }
        
        $status = empty($order) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $order);
    }
    
    /**
     * 设置订单状态
     *
     * @param int $orderId 订单id
     * @param int $status 状态值
     * @return 成功时返回true [1]
     */
    public function setStatus($orderId, $status)
    {
        if (empty($orderId)) return $this->buildResponse('error', 400, '$orderId cannot be empty');
        if (empty($status)) return $this->buildResponse('error', 400, '$status cannot be empty');
    
        if (NULL === ($order = ModOrder::find()->where(['id' => $orderId])->one())) return $this->buildResponse('failed', 400, 'order does not exist');
    
        //订单状态约束
        //支持订单状态[当前订单状态] = [可变更成的订单状态]
        //                 k = ['','']
        $change_to_status = [0 => [-1,1],
            1 => [2]
        ];
        //订单状态检测
        if(isset($change_to_status[$order['status']])){
            if(!array_search($status, $change_to_status[$order['status']])) return $this->buildResponse('error', 400, 'order status cannot be modified');
        }else{
            return $this->buildResponse('error', 400, 'Unsupported order status');
        }
    
        $order->setAttributes(['status' => $status]);

        if(FALSE != $order->save()){
            return $this->buildResponse('success', '201', true);
        }else{
            return $this->buildResponse('failed', 400, 'failed to add OrderGood resource');
        }
    
    }

	/**
	 * 获取所有订单
	 * @param array $map
	 * @param number $offset
	 * @param number $limit
	 * @param string $order
	 * @return \service\base\type
	 */
    public  function getAllOrders(array $map = [], $offset = 0, $limit = 20,$order='')
    {
    	if (!is_array($map)) return $this->buildResponse('error', 400, '$map must be array');
    	
    	$orders = ModOrder::getAllOrders($map, $offset, $limit,$order);
    	
    	$status = empty($orders) ? 200 : 201;
    	
    	return $this->buildResponse('success', $status, $orders);
    }

}