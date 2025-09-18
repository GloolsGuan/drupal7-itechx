<?php
namespace Ebouti\Boutique;
/* 
 * 购物车，基于Magento\Checkout\Model\Cart 开发
 * 
 * 备注：
 * 只是简单业务整理，修改部分较少，基本直接使用原Magento业务体系。
 * 
 * 保存商品购买说明信息（product_state）
 * $product_state instance of \Magento\Quote\Model\Quote\Item
 * 
 * 
 */

use \Magento\Framework\Exception\NoSuchEntityException;

class Cart extends \Gtools\Yii\Module{
    
    
    /**
     * Build an business object for checkout or transaction
     * 
     * What is an business object?
     * It is an instance of one of business type, e.g good, activity, service.
     * 
     * @param type $user_id
     * @param type $biz_id
     * @param type $object_args
     * 
     * @return business object(biz_object)
     * $biz_object = new stdClass();
     * $biz_object->user = [
     *   'user_id' => 0,
     *   'user_agent' => 0,
     *   'location' => '',
     *   'ip' => '',
     *   'device' => '',
     *   'operation_time' => ''
     * ];
     * 
     * $biz_object->business = [
     *   'biz_id' => 0,
     *   'instance_args' => []
     * ];
     */
    static public function buildBizObject($user_id, $biz_id, $object_args){
        
    }
    
    
    /**
     * Get customer cart with product list
     * @param type $customer_id
     * @return type
     */
    public function getCartForCustomer($customer_id){
        $ma_model_quotemanagement = $this->getMagentoOBjectManager()->create(\Magento\Quote\Model\QuoteManagement::class);
        $customer_cart = new \stdClass();
        try{
            $customer_cart = $ma_model_quotemanagement->getCartForCustomer($customer_id);
        }catch(NoSuchEntityException $e){
            $ma_model_quotemanagement->createEmptyCartForCustomer($customer_id);
        }
        
        $customer_cart = $ma_model_quotemanagement->getCartForCustomer($customer_id);
        
        //\Gtools\Debug::testLog(__METHOD__, [get_class($customer_cart), get_class($customer_cart_two)], __LINE__);
        return $customer_cart;
    }
    
    
    /**
     * 
     * @see \Magento\Checkout\Model\Cart
     * 
     * @param type $user_id
     * @param type $biz_id
     * @param type $product_state
     * @return type
     */
    public function add($customer_id, $product_id, $product_state){
        $ma_model_cart = $this->getMagentoObjectManager()->create(\Magento\Checkout\Model\Cart::class);
        $ma_model_customer = $this->getMagentoObjectManager()->create(\Magento\Customer\Model\Customer::class);
        
        $request_info = [];
        
        $ma_customer = $ma_model_customer->load($customer_id);
        $ma_customer_datamodel = $ma_customer->getDataModel();
        //\Gtools\Debug::testLog(__METHOD__, [get_class($ma_customer_datamodel), $ma_customer_datamodel instanceof \Magento\Customer\Api\Data\CustomerInterface], __LINE__);
        //$customer_cart = $this->getCartForCustomer($customer_id);
        $ma_model_cart->getCheckoutSession()->setCustomerData($ma_customer_datamodel);
               
        $ma_model_cart->addProduct($product_id, $request_info)->save();
        
        //$test_cart = $this->getCartForCustomer($customer_id);
        return $ma_model_cart->getQuote();
        //return $test_cart;
    }
    
    
    /**
     * Add single product and pay for it
     * 
     * @-- $address_state --
     * 
     */
    public function addSingle($customer_id, $product_id, $product_state){
        $ma_cart_management = $this->getMagentoObjectManager()->create(\Magento\Quote\Model\QuoteManagement::class);
        $ma_cart_repository = $this->getMagentoObjectManager()->create(\Magento\Quote\Model\QuoteRepository::class);
        $ma_model_customer = $this->getMagentoObjectManager()->create(\Magento\Customer\Model\Customer::class);
        $ma_model_productrepository = $this->getMagentoObjectManager()->create(\Magento\Catalog\Model\ProductRepository::class);
        
        $quote = $this->getCartForCustomer($customer_id);
        
        $quote->removeAllItems();
        $ma_product_modeldata = $ma_model_productrepository->getById($product_id);
        $quote->addProduct($ma_product_modeldata);
        //$quote->setExtShippingInfo('Hello,world!');
        
        //-- Set address of shipping --
        $address_model = $this->buildAddressModel($quote, $customer_id);
        $quote->setShippingAddress($address_model);
        $quote->setBillingAddress($address_model);

        $model_payment = $this->buildPayMent($quote);
        $quote->setPayment($model_payment);
        
        //-- [end of address setting] --
        $quote->save();
        //-----------
        
        //$test_quote = $this->getCartForCustomer($customer_id);
        //$ext_shipping_info = $test_quote->getExtShippingInfo();
        $order = $ma_cart_management->submit($quote);
        
        return $quote;
    }
    
    
    public function buildPayment(&$quote, $payment_state=[]){
        $ma_model_payment = $this->getMagentoObjectManager()->create(\Magento\Quote\Model\Quote\Payment::class);
        $ma_model_payment->setMethod('Hello,world!');
        $ma_model_payment->setQuote($quote);
        $ma_model_payment->save();
        return $ma_model_payment;
    }
    
    
    /**
     * 地理信息Module：\Magento\Directory
     * 地理信息数据库：ma_directory_country*
     * 
     * getShippingRatesCollection();  @return Magento\Quote\Model\ResourceModel\Quote\Address\Rate\Collection
     * 
     * @param type $quote
     * @param type $customer_id
     * @param array $address_state
     * @return type
     */
    public function buildAddressModel(&$quote, $customer_id, $address_state=[]){
        
        $shipping_method = 'normal';
        
        $address_model = $this->getMagentoObjectManager()->create(\Magento\Quote\Model\Quote\Address::class);
        
        $address_model->setCity('南京');
        $address_model->setQuote($quote);
        $address_model->setCustomerId($customer_id);
        $address_model->setCountryId('CN');
        $address_model->setStreet('江宁区东山街道土山路13-888');
        $address_model->setTelephone('13800000000');
        $address_model->setPostcode(210000);
        $address_model->setFirstname('Glools');
        $address_model->setLastname('Guan');
        //$address_model->setShippingMethod($shipping_method);
        
        $address_model->save();
        //-- Attach address rate --
        $rate_collection = $address_model->getShippingRatesCollection();
        $rate_state = [
          'Method' => 'normal',
          'MethodTitle' => 'Normal shipping（LAND）',
          'Price' => '12'
        ];
        $address_rate = $this->buildAddressRate($rate_state);
        $address_rate->setAddressId($address_model->getId());
        //--  \Magento\Sales\Model\Order::getShippingMethod() | list($carrierCode, $method) = explode('_', $shippingMethod, 2); --
        $address_rate->setCode(sprintf('SF-EXPRESS_%s', '陆运'));
        $address_model->setShippingMethod($address_rate->getCode());
        $rate_code = $address_rate->getCode();
        $rate_id = $address_rate->save();
        $address_model->save();
        \Gtools\Debug::testLog(__METHOD__, [$address_model->getId(), $address_rate->getId(), $address_rate->getCode(), $address_model->getCity(), $address_model->getTelephone(), $address_model->getFirstname(), $address_model->getLastname()], __LINE__);
        //-- Shipping method --
        //配送问题，涉及到地址、区域与费率问题，设置比较复杂，同时也关系到Magento系统对配送事务管理操作
        //$address_model->setShippingMethod('xxx');
        //$address_model->setFreeShipping(true);
        //$address_model->setCompany('');
        //$address_model
        
        return $address_model;
    }
    
    
    /**
     * 
     * 
     * @param type $address_model
     * @param type $rate_state
     * @return boolean
     */
    public function buildAddressRate($rate_state=[]){
        $address_rate = $this->getMagentoObjectManager()->create(\Magento\Quote\Model\Quote\Address\Rate::class);
        
        $allowed_fields = ['AddressId', 'Carrier', 'CarrierTitle', 'Code', 'Method', 'MethodDescription', 'Price', 'MethodTitle'];
        
        if(!is_array($rate_state)){
            return false;
        }
        
        foreach($rate_state as $k=>$v){
            if(!in_array($k, $allowed_fields)){
                continue;
            }
            
            $address_rate->$k = $v;
        }
        return $address_rate;
    }
    
    
    
    public function update($user_id, $iz_id, $product_state){
        
    }
    
    
    public function remove($user_id, $biz_id){
        
    }
    
    
    /**
     * Build checkout state for building order and checkout.
     * 
     * The returned data collection is not order entity, but they are an part of order entity.
     * 
     * @param type $user_id
     * @param type $biz_ids
     */
    public function buildCheckoutState($user_id, $biz_ids){
        
    }
    
    
}