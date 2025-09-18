<?php
namespace Ebouti\Boutique;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Activity extends Base\Goods{
    
    const STORE_CODE = 'activity';
    
    public function load($activity_id, $user_id=0){
        $activity = $this->_load($activity_id, $user_id);
        
        if (false==$activity) {
            return $this->buildResponse(200,  'success', $activity);
        }
        
        return $this->buildResponse(201, 'success', $activity);
    }
    
    public function retrieve($user_id, $status, $page=1, $rows=30, $ext_args = array()) {
        
        
        $omInstance = $this->getMagentoObjectManager();
        $store_entity = $this->loadStoreEntity(self::STORE_CODE);
        $products = $this->_retrieve($store_entity['group_id']);
        
        //\Gtools\Debug::testLog(__METHOD__, [$store_entity, count($products)], __LINE__);
        if(empty($products)){
            return $this->buildResponse(200, 'success', []);
        }
        
        //\Gtools\Debug::testLog(__METHOD__, $products, __LINE__);
        return $this->buildResponse(201, 'success', $products);
    }
    
    public function getBizType(){
        return [
            'id' => 9,
            'title' => '活动'
        ];
    }
}