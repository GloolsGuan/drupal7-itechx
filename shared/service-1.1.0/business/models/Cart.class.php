<?php
/**
 * 购物车模型定义
 */

namespace service\business\models;

use \service\base\db\SimpleAR;
use \service\plan\models\Plan;


class Cart extends SimpleAR
{


    public static function tableName()
    {
        return 'biz_cart';
    }

    public static function getCarts(array $map = [])
    {
        $query = static::find()->from(['cart' => static::tableName()])
            ->select(['cart.*', 'plan.plan_name', 'plan.plan_bt'])
            ->join('LEFT JOIN', Plan::tableName() . ' AS plan', 'plan.plan_id = cart.course_id')
            ->where($map);

        $carts = $query->asArray()->all();

        return $carts;
    }

}