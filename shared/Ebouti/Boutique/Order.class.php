<?php
namespace Ebouti\Boutique;

/** 
 * Order management and process
 * 
 * #### Magento2.x 订单部分代码分析说明 ####
 * ###### 订单创建 ######
 * 订单创建依赖Checkout类型
 * 
 * @See:
 * 订单创建控制器：\Magento_Checkout\Controller\Onepage\Saveorder();
 * 订单创建业务逻辑部分：\Magento_Checkout\Model\Type\Onepage::saveOrder();
 * 
 * 
 * 
 */



class Order extends \Gtools\Yii\Module{
    
    
    static public function buildOrderEntity($user_id,$biz_type, $checkout_state, $express_state){
        
    }
    
    
    /**
     * Create an new order
     * 
     * What is required on creating an new order?
     * bla,bla,bla
     * 
     * 
     * When to create an order?
     * On checkout with an business list, e.g goods, services, activities
     * 
     * @see Models map:
     * $ma_model_quote->addAddress($instance); \Magento\Quote\Model\Quote
     * 
     * 约定：
     * 报价：Magento\Quote\Model\Quote, 指服务于一个订单的报价
     * 报价项目：Magento\Quote\Model\Quote\Item 指一个报价包含的多个商品项
     * 
     * 问题：
     * 一次Checkout包含多个快递地址时如何处理？
     * 1. 默认不支持一个订单或一次checkout多个快递地址。
     * 2. 非要多个地址只能创建多个订单，独立结算。
     * 3. 
     * 
     * 基本规范（我的神呢，Magento）：
     * 1. 一个订单只接受一个报价。
     * 2. 一个报价只有一个地址。
     * 3. 一个报价可以包含多个报价项目。
     * 4. 每个报价项目只包含商品购买信息
     * 5. 一个报价下
     * 
     * @param type $user_id
     * @param type $order_entity
     */
    public function create($user_id, $product_checkout_state){
        $obj_manager = $this->getMagentoObjectManager();
        $ma_model_onepage = $obj_manager->create(\Magento\Checkout\Model\Type\Onepage::class);
        $ma_model_quote_item = $obj_manager->create(\Magento\Quote\Model\Quote\Item::class);
        $ma_model_quote_manager = $obj_manager->create(\Magento\Quote\Model\QuateManagement::class);
        
        //-- @see: \Magento\Checkout\Model\Session --
        //-- $ma_model_quote is an instance of "\Magento\Quote\Model\Quote"
        $ma_model_quote = $ma_model_onepage->getQuote();
        
        //-- $product_checkout_state --
        
        
        //-- Step:1 --
        // \Magento\Catalog\Model\Product $product
        $ma_model_quote_item->setName();
        $ma_model_quote_item->setOptions();
        $ma_model_quote_item->setProduct();
        $ma_model_quote_item->setQuote();
        $ma_model_quote_item->setQty();
        $ma_model_quote_item->setQtyOptions();
        
        $ma_model_quote->addItem($ma_model_quote_item);
        
        $ma_model_quote->setStoreId(2);
        \Gtools\Debug::testLog(__METHOD__, [
            'get_store_id' => $ma_model_quote->getStoreId()
        ], __LINE__);
        
        
        //$order = $ma_model_quote_manager->submit($ma_model_quote);
    }
    
    public function load(){
        
    }
    
    public function loadSchedule(){
        
    }
    
    public function cancel($user_id, $order_id, $reason=''){
        
    }
    
    
    /**
     * Process order under workflow rule
     * 
     * @param type $order_id
     * @param type $order_status
     * @param type $process_state
     */
    public function process($order_id, $order_status, $process_state){
        
    }
    
    
}