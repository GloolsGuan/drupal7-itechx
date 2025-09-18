<?php
namespace Ebouti\Boutique;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Service extends \Gtools\Yii\Module{
    
    public $object_manager = null;
    
    const STORE_CODE = 'service';
    
    public function load($service_id, $user_id=0){
        $service = $this->_load($service_id, $user_id);
        if (false==$service) {
            return $this->buildResponse(200,  'success', []);
        }
        
        return $this->buildResponse(201, 'success', $service);
    }
    
    
    public function loadServices($args, $status='activated'){
        //\Gtools\Debug::testLog(__METHOD__, get_class($this->ma_obj_manager), __LINE__);
        $omInstance = $this->getMagentoObjectManager();
        $model_product_renderlist = $omInstance->create(\Magento\Catalog\Model\ProductRenderList::class);
        $ma_search_criteria = $omInstance->create(\Magento\Framework\Api\SearchCriteria::class);
        $model_category = $omInstance->create(Magento\Catalog\Model\Category::class);
        $products = $model_product_renderlist->getList($ma_search_criteria, 1, 'detail');
       
        //-- 获取属性entity信息的SQL --
        // select * from attribute_id,attribute_code, frontend_label from ma_eav_attribute;
        //$xx = $model_category->loadByAttribute('id', 3);
        //\Gtools\Debug::testLog(__METHOD__, get_class($xx), __LINE__);
        
        $items = $products->getItems();
        
        foreach($items as $item){
            //\Gtools\Debug::testLog(__METHOD__, get_class($item), __LINE__);
            $pro_infos[] = $this->buildProductData($item, 'list');
        }
        
        return $this->buildResponse(300, 'test', $pro_infos);
    }
    
    
    public function getBizType(){
        return [
            'id' => 10,
            'title' => '服务'
        ];
    }
    
    
    protected function buildProductData(\Magento\Catalog\Model\ProductRender $product, $view_mode='applist'){
        //$images = $item->getImages();
        //$media_images = $item->getMediaGallery();
        return ['Hello,world', $product->getName()];
    }
}