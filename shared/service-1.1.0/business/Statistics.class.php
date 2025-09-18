<?php
/**
 *
 */

namespace service\business;


use service\base\Base;
use service\business\interfaces\Statistics as StatisticsInterface;
use service\base\Module;
use service\business\models\Order as OrderModel;
use service\business\models\OrderGoods as OrderGoodsModel;
use service\business\models\OrderGoods;
use yii\helpers\ArrayHelper;


class Statistics extends Module implements StatisticsInterface
{

    /**
     * 订单列表
     * @param array $map    查询条件
     * @param string $group
     * @param int $offset
     * @param int $limit
     * @return array|\service\base\type
     */
    public function statistics(array $map = [], $group = '', $offset = 0, $limit = 20)
    {
            $result = OrderModel::find()
                        ->from(['order'=>OrderModel::tableName()])
                        ->select(['order.*','goods.*'])
                        ->groupBy('order_id')
                        ->where($map)
                        ->leftjoin(['goods'=>OrderGoods::tableName()], 'order.id = goods.order_id')
                        ->asArray()
                        ->all();

        $uniqueCodes = ArrayHelper::getColumn($result, 'unique_code');

        $orderId = ArrayHelper::getColumn($result, 'order_id');

        //在user表中查询用户信息
        $userService = Base::loadService('service\user\User');

        $usersData = $userService->loadListByCodes($uniqueCodes);

        if ($this->isErrorResponse($usersData)) {
            return $usersData;
        }

        $usersData = $usersData['data'];

        $usersData = ArrayHelper::index($usersData, 'unique_code');

        foreach ($orderId as $val) {
            $data[$val] = OrderGoodsModel::find()->select(['order_id', 'course_id', 'course_name'])->where(['order_id' => $val])->asArray()->all();
        }

        foreach ($result as &$item) {
            $item['goods'] = $data[$item['order_id']];
            $item['user_name'] = ArrayHelper::getValue(ArrayHelper::getValue($usersData, $item['unique_code'], []), 'user_name', '');
            //$usersData[$item['unique_code']]['user_name'];
        };

        return $this->buildResponse('success', 201, $result);

    }

    /**
     * 财务统计列表查询
     *
     * @param array $map  查询条件
     * @return array|\service\base\type
     */
    public function serachFinancial(array $map = [])
    {

        if (empty($map)) {
            return $this->buildResponse('error', 400, '$map cannot be empty');
        }

        $result = OrderModel::find()->where($map)->asArray()->all();

        $unique_code = ArrayHelper::getColumn($result, 'unique_code');

        //在user表中查询用户信息
        $userService = Base::loadService('service\user\User');

        $usersData = $userService->loadListByCodes($unique_code);

        if ($this->isErrorResponse($usersData)) {
            return $usersData;
        }

        $usersData = ArrayHelper::index($usersData['data'], 'unique_code');

        foreach ($result as $key => &$val) {
            $val['user_name'] = $usersData[$val['unique_code']]['user_name'];
        }

        return $this->buildResponse('success', '201', $result);

        /*
        $where[] = 'AND';
        $where[] = ['bt_date' => '2016-12-01 09:00:00'];
        $where[] = ['et_date' => '2016-12-01 09:00:00'];
        $where[] = ['company' => '单位'];
        $where[] = ['from' => '来源'];
        $where[] = ['type' => '出入账种类'];
        $where[] = ['note' => '详情/备注'];

        $lists = [
            [
                'id' => 1,
                'pay_time' => '',// 交易时间
                'type' => '',//出入账种类
                'amount' => '',//金额
                 'student' => '',//学员

                'from' => '',//来源
                'wxNumbers' => '',//微信单号
                'company' => '',//单位
                'note' => '',//详情/备注
            ]
        ];
        */
    }



}