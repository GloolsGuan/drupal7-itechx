<?php
/**
 * Created by PhpStorm.
 * User: Win10
 * Date: 2016/11/18
 * Time: 16:24
 */

namespace service\business;

use \service\base\Module;
use \service\business\interfaces\CartTrait;
use \service\business\models\Cart AS ModCart;
use \service\business\interfaces\Cart AS CartInterface;


/**
 * 购物车功能，对应数据表biz_cart
 * 商品标识功能(tag字段)可暂不开发[yangzy 20161125]
 * 本接口与CartTrait一同使用
 *
 * @package service\plan\interfaces
 * @design yangzy 20161123
 * @author drce 20161129
 */
 
class Cart extends Module implements CartInterface
{
    use CartTrait;
    
    /**
     * 根据标识获取购物车中所有商品
     *
     * @param string $tag 标识
     * @return array [1]
     */
    public function getGoods($tag = '')
    {
        if(empty($this->userId)) return $this->buildResponse('error', 400, '$userId must be set');
        
        $cart = ModCart::getCarts(['unique_code' => $this->userId]);
        
        $status = empty($cart) ? 201 : 200;
        
        return $this->buildResponse('success', $status, $cart);
    }
    
    /**
     * 根据id获取商品详情
     *
     * @param int|array $goodIds 商品id
     * @return array [1]
    */
    public function getGoodsById($goodIds)
    {
        if(empty($this->userId)) return $this->buildResponse('error', 400, '$userId must be set');
        
        if (empty($goodIds)) return $this->buildResponse('error', 400, '$goodIds cannot be empty');
        
        $cart = ModCart::find()->where(['id' => $goodIds,'unique_code' => $this->userId])->asArray()->all();
        
        $status = empty($cart) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $cart);
    }
    
    /**
     * 向购物车中添加商品
     *
     * @param array $good 商品数据 格式参照，订单商品表的字段设置
     * @param string $tag 分类标识
     * @return array 成功时返回新添加的商品数据 [1]
    */
    public function addGood($good, $tag = '')
    {
        if(empty($this->userId)) return $this->buildResponse('error', 400, '$userId must be set');
        
        if ( !is_array($good) ) return $this->buildResponse('error', 400, '$good cannot be array');
        
        !isset($good['num']) ? $good['num'] = 1 : NULL;
        $good['unique_code'] = $this->userId;
        $good['update_time'] = date('Y-m-d H:i:s');
        
        //存在购物车id，自动处理商品数量
        if( isset($good['id']) && !empty($good['id']) ){
            if( NULL !== ($old_good = ModCart::find()->where(['id' => $good['id'],'unique_code' => $good['unique_code']])->one()) ){
                $good['num'] = $old_good['num']+$good['num'];
                $new_good = $old_good->setAttributes($good);
            }else{
                return $this->buildResponse('error', 400, 'failed to add cart');
            }
        }else{
            $can_not_empty = ['unique_code', 'num', 'price', 'course_id'];
            foreach ($can_not_empty as $key => $val){
                if ( !isset($good[$val]) || empty($good[$val]) ) return $this->buildResponse('error', 400, $val.' cannot be empty');
            }
            $new_good = (new ModCart())->setAttributes($good);
        }
        
        if ( FALSE === $new_good->save() )
            return $this->buildResponse('failed', 201, 'failed to add cart');
        else
            return $this->buildResponse('success', 200, $new_good->getAttributes());
        
    }
    
    /**
     * 从购物车中删除商品
     *
     * @param int|array $goodIds 购物车中id
     * @return array 成功时返回true [1]
    */
    public function deleteGood($goodIds)
    {
        if(empty($this->userId)) return $this->buildResponse('error', 400, '$userId must be set');
        
        if ( empty($goodIds) || !is_array($goodIds) ) return $this->buildResponse('error', 400, '$goodIds cannot be array');
        
        $deleteParm = ['AND', ['=', 'unique_code', $this->userId], ['in', 'id', $goodIds]];
        
        if (FALSE === ModCart::deleteAll($deleteParm)) return $this->buildResponse('failed', 400, 'failed to remove Carts resource');
        
        return true;
    }
    
    /**
     * 更新购物车中商品信息
     *
     * @param int $goodId 商品id
     * @param array $data
     * @return mixed 成功时返回true
    */
    public function updateGood($goodId, array $data)
    {
        if(empty($this->userId)) return $this->buildResponse('error', 400, '$userId must be set');
        
        if (empty($goodId)) return $this->buildResponse('error', 400, '$id cannot be empty');
        if (empty($data) || !is_array($data)) return $this->buildResponse('error', 400, '$attributes must be an array');
        
        $good['unique_code'] = $this->userId;
        $data['update_time'] = date('Y-m-d H:i:s');
        
        if (NULL === ($cart = ModCart::find()->where(['id' => $goodId,'unique_code' => $good['unique_code']])->one())) return $this->buildResponse('failed', 400, 'cart does not exist');
        
        $cart->setAttributes($data);
        
        if (false === $cart->save()) return $cart->buildResponse('failed', 400, 'failed to edit Cart resource');
        
        return true;
    }
    
    /**
     * 删除购物车中有对应标识的商品
     *
     * @param string $tag 标识
     * @return 成功时返回true
    */
    public function clearGoodsByTag($tag = '')
    {
        if(empty($this->userId)) return $this->buildResponse('error', 400, '$userId must be set');
        
        if ( empty($tag) ) return $this->buildResponse('error', 400, '$tag cannot be array');
        
        $deleteParm = ['AND', ['=', 'unique_code', $this->userId], ['=', 'tag', $tag]];
        
        if (FALSE === ModCart::deleteAll($deleteParm)) return $this->buildResponse('failed', 400, 'failed to remove Carts resource');
        
        return true;
    }
    
    /**
     * 清空购物车
     *
     * @return 成功返回true [1]
     */
    public function clearCart()
    {
        if(empty($this->userId)) return $this->buildResponse('error', 400, '$userId must be set');
        
        $deleteParm = ['=', 'unique_code', $this->userId];
        
        if (FALSE === ModCart::deleteAll($deleteParm)) return $this->buildResponse('failed', 400, 'failed to remove Carts resource');
        
        return true;
    }
    
}