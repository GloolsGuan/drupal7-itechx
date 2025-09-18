<?php

namespace service\business;

use \service\base\Module;
use \service\business\models\Refund as ModRefund;
use \service\business\models\OrderGoods as ModOrderGoods;
use \service\business\models\RefundGoods as ModRefundGoods;
use \service\business\interfaces\Refund as RefundInterface;
use \service\business\interfaces\RefundGoods as RefundGoodsInterface;

class Refund extends Module implements RefundInterface,RefundGoodsInterface
{
    /**
     * 创建退单申请
     * @param array $refund 申请详情，数据字段参照数据表biz_refund
     * @return mixed
     * @author drce 20161124
     */
    public function createRefund(array $refund = [])
    {
        $can_not_empty = array('order_id','unique_code','course_id');
        
        foreach ($can_not_empty as $key => $val){
            if ( !isset($refund[$val]) || empty($refund[$val]) ) return $this->buildResponse('error', 400, $val.' cannot be empty');
        }
        
        if( !isset($refund['amount']) ) return $this->buildResponse('error', 400, 'amount cannot be empty');
        !isset($refund['create_time']) ? $refund['create_time'] = date("Y-m-d H:i:s",time()) : NULL;
        
        $refund['status'] = ModRefund::STATUS_UNAUDITED;
        
        $model = new ModRefund();
        $model->setAttributes($refund);
        
        if (false === $model->save())
            return $this->buildResponse('failed', 400, 'failed to add Order resource');
        else
            return $this->buildResponse('success', 201, $model->getAttributes());
    }
    
    /**
     * 查询退单申请详情
     *
     * @param int $id 申请id
     * @return array 申请详情 统一格式
     * @author drce 20161124
    */
    public function getRefund($id)
    {
        if (empty($id)) return $this->buildResponse('error', 400, '$id cannot be empty');
        
        if (NULL === ($refund = ModRefund::find()->where(['id' => $id])->one())) return $this->buildResponse('failed', 400, 'refund does not exist');
        
        $status = empty($rows = $refund->getAttributes()) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $rows);
    }
    
    /**
     * 获取退单列表
     *
     * @param array $map 查询条件，格式参照yii2 where()方法
     * @param int $offset 数据偏移量
     * @param int $limit 条数限制
     * @return array 退单列表数据 统一格式
     * @author drce 20161124
    */
    public function getRefunds(array $map = [], $offset = 0, $limit = 20)
    {
        if (!is_array($map)) return $this->buildResponse('error', 400, '$map must be array');
        
        $refunds = ModRefund::getRefunds($map, $offset, $limit);
        
        $status = empty($refunds) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $refunds);
    }
    
    
    /**
     *统计退单数
     *
     * @param array $map 约束条件，格式参照yii2 where()方法
     * @return array 退单数 统一格式
     * @author shenf 20161124
    */
    public function countRefunds(array $map = [])
    {
        if (!is_array($map)) return $this->buildResponse('error', 400, '$map must be array');
        
        $refund_num = ModRefund::countRefunds($map);
        
        $status = empty($refund_num) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $refund_num);
    }
    
    
    /**
     * 审核退单，标记退单状态
     *
     * @param int $refundId 退单id
     * @param bool $approved 是否通过审核
     * @return array 统一格式
     * @author drce 20161124
    */
    public function auditRefund($refundId, $approved = true)
    {
        if (empty($refundId)) return $this->buildResponse('error', 400, '$refundId cannot be empty');
        
        $refund = ModRefund::auditRefund($refundId);
        
        if (!is_array($refund)){
            if ('REFUND_LOSE' == $refund) return $this->buildResponse('error', 400, 'refund does not exist');
            if ('STATUS_ERROR' == $refund) return $this->buildResponse('error', 400, 'refund status error');
            if ('SAVE_ERROR' == $refund) return $refund->buildResponse('failed', 400, 'failed to save refund resource');
        }
        
        $status = empty($refund) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $refund);
    }
    
    /**
     * 向退单申请中添加已购买到的产品
     * 只有已购买到的商品才能被添加成退单申请中
     * 添加商品的时候同时要更改订单商品表中的商品的状态为退费中
     *
     * @param int $refundId 退单id
     * @param int|array $orderGoodId 订单商品表中的id 可以为数字或数组
     * @return 成功时返回true [1]
     * @author drce 20161201
     */
    public function addGood($refundId, $orderGoodId)
    {
        if (empty($refundId)) return $this->buildResponse('error', 400, '$refundId cannot be empty');
        if (empty($orderGoodId)) return $this->buildResponse('error', 400, '$orderGoodId must be array');
        
        if (is_array($orderGoodId)){
            foreach ($orderGoodId as $k) {
                $rows[] = ['refund_id' => $refundId,'order_good_id' => $k,'status' => 1];
            }
        }else{
             $rows[] = ['refund_id' => $refundId,'order_good_id' => $orderGoodId,'status' => 1];
        }
        
        //订单商品校验
        $goods = ModOrderGoods::getGoods(['id' => $orderGoodId],0,100);

        $temp_goods = [];
        foreach ($goods as $k => $v){
            $temp_goods[] = $v['id'];
        }

        $diff = array_diff($orderGoodId,$temp_goods);
        
        if(!empty($diff)){
            return $this->buildResponse('failed', 400, 'goods ' . implode(",",$diff) . ' was not found');
        }
        
        if(FALSE === \Yii::$app->db->createCommand()->batchInsert(ModRefundGoods::tableName(), ['refund_id', 'order_good_id', 'status'], $rows)->execute()) return $this->buildResponse('failed', 400, 'failed to add RefundGoods resource');
        
        return true;
    }
    
    /**
     * 从退单申请中移除要退的商品
     * 删除商品的时候同时要更改订单商品表中的商品的状态为审核通过
     *
     * @param int $refundId 退单申请的id
     * @param int|array $refundGoodId 退单商品表中的id 可以为数字或数组
     * @return 成功时返回true [1]
     * @author drce 20161201
    */
    public function removeGood($refundId, $refundGoodId)
    {
        if (empty($refundId)) return $this->buildResponse('error', 400, '$refundId cannot be empty');
        if (empty($refundGoodId)) return $this->buildResponse('error', 400, '$refundGoodId must be array');
        
        if (NULL === (ModRefund::find()->select(['id'])->where(['id' => $refundId])->one())) return $this->buildResponse('failed', 400, 'RefundOrder does not exist');
        
        $deleteParm = array();
        
        if (is_array($refundGoodId)){
            $deleteParm = [ ['and','refund_id = :refundId',['in', 'order_good_id', $refundGoodId]], [':refundId' => $refundId] ];
        }else{
            $deleteParm = [ ['and','refund_id = :refundId','order_good_id = :refundGoodId'], [':refundId' => $refundId,':refundGoodId' => $refundGoodId] ];
        }
        
        if (FALSE === ModRefundGoods::deleteAll($deleteParm[0],$deleteParm[1])) return $this->buildResponse('failed', 400, 'failed to remove RefundOrder resource');
        
        return true;
    }
    
    /**
     * 获取退单申请中包括的商品
     * @param int $refundId 退单申请的id
     * @return 成功时返回商品列表 [1]
     * @author drce 20161201
    */
    public function getRefundGoods($refundId)
    {
        if (empty($refundId)) return $this->buildResponse('error', 400, '$refundId cannot be empty');
        
        $refund_goods = ModRefundGoods::getGoods(['refund_id' => $refundId]);
        
        $status = empty($refund_goods) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $refund_goods);
    }
    
}