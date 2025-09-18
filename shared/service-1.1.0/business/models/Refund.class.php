<?php
/**
 * 退单模型定义
 */

namespace service\business\models;

use \service\base\db\SimpleAR;


class Refund extends SimpleAR
{
    
    /**
     * 退单状态：-1：取消，0：待退款，1：已退款，2：待审核，3：审核通过，4：审核未通过
     */
    const STATUS_CANCELED = -1;
    const STATUS_UNRETURNED = 0;
    const STATUS_RETURNED = 1;
    const STATUS_UNAUDITED = 2;
    const STATUS_AUDITED = 3;
    const STATUS_FAILAUDITED = 4;
    
    public static function tableName()
    {
        return 'biz_refund';
    }
    
    /**
     * 获取退单列表
     *
     * @param array $map 查询条件，格式参照yii2 where()方法
     * @param int $offset 数据偏移量
     * @param int $limit 条数限制
     * @return array 退单列表数据 统一格式
     * @author shenf 20161124
     */
    public static function getRefunds(array $map = [], $offset = 0, $limit = 20)
    {
        $query = static::find()->from(['refund' => static::tableName()])
        ->select([
            'refund.*'
        ])
        ->where($map);
    
        $refunds = $query->offset($offset)->limit($limit)->orderBy('refund.id DESC')->asArray()->all();
    
        return [$refunds, $query->count()];
    }
    
    /**
     *统计退单数
     *
     * @param array $map 约束条件，格式参照yii2 where()方法
     * @return array 退单数 统一格式
     * @author shenf 20161124
     */
    public static function countRefunds(array $map = [])
    {
        $query = static::find()->from(['refund' => static::tableName()])
        ->select([
            'refund.*'
        ])
        ->where($map);
    
        return $query->count();
    }
    
    /**
     * 审核退单，标记退单状态
     *
     * @param int $refundId 退单id
     * @param bool $approved 是否通过审核
     * @return array 统一格式
     * @author shenf 20161124
     */
    public static function auditRefund($refundId, $approved = true)
    {
        if (NULL === ($refund = static::find()->where(['id' => $refundId])->one())) return 'REFUND_LOSE';
    
        if (static::STATUS_UNAUDITED != $refund['status']) return 'STATUS_ERROR';
    
        $approved ? //审核通过
            $attributes = ['status' => static::STATUS_AUDITED,'review_time' => date("Y-m-d H:i:s",time())]
            : //审核失败
            $attributes = ['status' => static::STATUS_FAILAUDITED,'review_time' => date("Y-m-d H:i:s",time())]
        ;
    
        $refund->setAttributes($attributes);
    
        if (false === $refund->save()) return 'SAVE_ERROR';
    
        return $refund->getAttributes();
    }
    
}