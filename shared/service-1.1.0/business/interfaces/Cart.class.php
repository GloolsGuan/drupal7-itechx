<?php
/**
 *
 */

namespace service\business\interfaces;

//use \service\business\interfaces\Order;

/**
 * 购物车功能，对应数据表biz_cart
 * 商品标识功能(tag字段)可暂不开发[yangzy 20161125]
 * 本接口与CartTrait一同使用
 *
 * @package service\plan\interfaces
 * @design yangzy 20161123
 * @author shenf 20161129
 */
interface  Cart
{
    /**
     * 根据标识获取购物车中所有商品
     *
     * @param string $tag 标识
     * @return array [1]
     */
    public function getGoods($tag = '');

    /**
     * 根据id获取商品详情
     *
     * @param int|array $goodIds 商品id
     * @return array [1]
     */
    public function getGoodsById($goodIds);

    /**
     * 向购物车中添加商品
     *
     * @param array $good 商品数据 格式参照，订单商品表的字段设置
     * @param string $tag 分类标识
     * @return array 成功时返回新添加的商品数据 [1]
     */
    public function addGood($good, $tag = '');

    /**
     * 从购物车中删除商品
     *
     * @param int|array $goodIds 购物车中id
     * @return array 成功时返回true [1]
     */
    public function deleteGood($goodIds);

    /**
     * 更新购物车中商品信息
     *
     * @param int $goodId 商品id
     * @param array $data
     * @return mixed 成功时返回true
     */
    public function updateGood($goodId, array $data);

    /**
     * 删除购物车中有对应标识的商品
     *
     * @param string $tag 标识
     * @return 成功时返回true
     */
    public function clearGoodsByTag($tag = '');

    /**
     * 清空购物车
     *
     * @return 成功返回true [1]
     */
    public function clearCart();

}