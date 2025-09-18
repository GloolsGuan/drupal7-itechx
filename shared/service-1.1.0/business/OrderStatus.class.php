<?php

namespace service\business;

use \service\base\Module;
use \service\business\models\Order as ModOrder;
use \service\business\interfaces\OrderStatus as OrderStatusInterface;

class OrderStatus extends Module implements OrderStatusInterface
{
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
            if(array_search($status, $change_to_status[$order['status']])) return $this->buildResponse('error', 400, 'order status cannot be modified');
        }else{
            return $this->buildResponse('error', 400, 'Unsupported order status');
        }
        
        $order->setAttributes(['status' => $status]);
        
        if(FALSE === $order->save()){
            return $this->buildResponse('success', '201', true);
        }else{
            return $this->buildResponse('failed', 400, 'failed to add OrderGood resource');
        }
        
    }
    
}