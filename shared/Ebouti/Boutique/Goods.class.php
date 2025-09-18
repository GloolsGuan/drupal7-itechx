<?php
namespace Ebouti\Boutique;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class Goods extends Base\Goods{
    
    
    const STORE_CODE = 'boutique';
    
    public function load($good_id, $user_id=0){
        $good = $this->_load($good_id, $user_id);
        if (false==$good) {
            return $this->buildResponse(200,  'success', []);
        }
        
        return $this->buildResponse(201, 'success', $good);
    }
    
    
   
    
    /**
     * Load good structure with full of fields settings.
     * 
     * -- test --
     * $attributes['entity_id'] | Magento\Catalog\Model\ResourceModel\Eav\Attribute\Interceptor
     */
    public function loadStructure($good_id=null){
        if(empty($good_id)){
            return $this->buildResponse(400, 'error', 'Invalid parameter "product_id" provided, It is must be numeric.');
        }
        
        $good_entity = $this->load($good_id);
        
        $structure_model = $this->buildStructure(
            $good_entity->bundle_name,
            $good_entity->bundle_version,
            $good_entity
        );
        
        return $this->buildResponse(300, 'testing', $structure_model);
    }
    
    
    public function buildStructure($bundle_name, $bundle_version, $good_entity){
        
    }
    
    
    
    public function retrieve($user_id, $status, $page=1, $rows=30, $ext_args = array()) {
        
        $omInstance = $this->getMagentoObjectManager();
        //-- To be sure, store_id is equal to store_group_id --
        $store_entity = $this->loadStoreEntity(self::STORE_CODE);
        $products = $this->_retrieve($store_entity['group_id']);
        if(empty($products)){
            return $this->buildResponse(200, 'success', []);
        }
        
        //\Gtools\Debug::testLog(__METHOD__, $products, __LINE__);
        return $this->buildResponse(201, 'success', $products);
    }
    
    
    public function getBizType(){
        return [
            'id' => 4,
            'title' => '商品'
        ];
    }
}
