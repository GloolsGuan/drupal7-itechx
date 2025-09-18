<?php
namespace Ebouti\Boutique\Base;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use \Ebouti\Boutique\MagentoHelper\Criteria as MaCriteria;

abstract class Goods extends \Gtools\Yii\Module{
    
    
    const STORE_CODE = '';
    
    
    public function init(){
        $called_store_code = $this->getStoreCode();
    }
    
    protected function _load($id, $user_id=0){
        $omInstance = $this->getMagentoObjectManager();
        //\Gtools\Debug::testLog(__METHOD__, $store_id, __LINE__);
        $model_product = $omInstance->create(\Magento\Catalog\Model\Product::class);
        
        $magento_product_model = $model_product->load($id);
        //$data = $magento_product_model->getData();
        //$keys = array_keys($data);
        
        if (false==$magento_product_model || empty($magento_product_model)){
            return false;
        }
        
        $product = $this->_buildProductDataForLoad($magento_product_model);
        
        //\Gtools\Debug::testLog(__METHOD__, $product, __LINE__);
        
        return $product;
    }
    
    
    /**
     * array(39) {
        [0]=>
        string(9) "entity_id"
        [1]=>
        string(16) "attribute_set_id"
        [2]=>
        string(7) "type_id"
        [3]=>
        string(3) "sku"
        [4]=>
        string(11) "has_options"
        [5]=>
        string(16) "required_options"
        [6]=>
        string(10) "created_at"
        [7]=>
        string(10) "updated_at"
        [8]=>
        string(4) "name"
        [9]=>
        string(10) "meta_title"
        [10]=>
        string(16) "meta_description"
        [11]=>
        string(5) "image"
        [12]=>
        string(11) "small_image"
        [13]=>
        string(9) "thumbnail"
        [14]=>
        string(17) "options_container"
        [15]=>
        string(7) "url_key"
        [16]=>
        string(22) "gift_message_available"
        [17]=>
        string(12) "swatch_image"
        [18]=>
        string(13) "attr_08b4b13d"
        [19]=>
        string(13) "attr_2e20941d"
        [20]=>
        string(6) "status"
        [21]=>
        string(10) "visibility"
        [22]=>
        string(25) "quantity_and_stock_status"
        [23]=>
        string(12) "tax_class_id"
        [24]=>
        string(13) "attr_d8feb4c1"
        [25]=>
        string(13) "attr_acd45bca"
        [26]=>
        string(13) "attr_601f100d"
        [27]=>
        string(5) "price"
        [28]=>
        string(13) "special_price"
        [29]=>
        string(11) "description"
        [30]=>
        string(17) "short_description"
        [31]=>
        string(12) "meta_keyword"
        [32]=>
        string(7) "options"
        [33]=>
        string(13) "media_gallery"
        [34]=>
        string(20) "extension_attributes"
        [35]=>
        string(10) "tier_price"
        [36]=>
        string(18) "tier_price_changed"
        [37]=>
        string(12) "category_ids"
        [38]=>
        string(10) "is_salable"
      }

     * @param type $product
     * @return type
     */
    protected function _buildProductDataForLoad($product){
        
        //$test = new \ReflectionClass($product);
        $omInstance = $this->getMagentoObjectManager();
        $stock_model = $omInstance->create(\Magento\CatalogInventory\Model\StockState::class);
        
        $entity_data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'type' => $product->getTypeId(),
            'status' => $product->getData('status'),
            'is_salable' => $product->getIsSalable(),
            'store_id' => $product->getStoreId(),
            //'currency_code' => $product->getData(),
            'sku' => $product->getSku(),
            'unit' => strip_tags($product->getData('attr_2e20941d')),
            'stock_quantity' => $stock_model->getStockQty($product->getId()),
            'stock_status' => $product->getData('quantity_and_stock_status'),
            //'ext_attributes' => $product->getExtensionAttributes(),
            'images' =>  [],//$product->getImages(),
            'resource_name' => $product->getResourceName(),
            'price_info' => $product->getFinalPrice(),
            'brief' => strip_tags($product->getData('short_description')),
            'description' => $product->getData('description'),
            'viewmode_list_style' => $this->translateListViewStyle($product->getData('attr_d8feb4c1')),
            'viewmode_is_poster' => $product->getData('attr_acd45bca'),
            'viewmode_recommended' => $product->getData('attr_601f100d'),
            'address_key' => strip_tags($product->getData('attr_08b4b13d'))
        ];
        
        $media_images = $product->getMediaGalleryImages()->getItems();
        
        foreach($media_images as $image){
            $entity_data['images'][] = [
                'position'=>$image->getPosition(), 
                'url'=>$image->getUrl(), 
                'id' => $image->getId()
            ];
        }
        
        //-- array item: Magento\Catalog\Model\Product\Option --
        $options = $product->getOptions();
        
        foreach($options as $ok=>$ov){
            $values = $ov->getValues();
            
            if (is_array($values)){
                $test_value = array_pop($values);
                \Gtools\Debug::testLog(__METHOD__, [$ok, $ov->getType(), $ov->getTitle(), $test_value->getTitle()], __LINE__);
            }
        }
        return $entity_data;
    }
    
    
    
    abstract public function getBizType();
    
    
    /**
     * Load store group(store) entity
     * @param type $store_code
     * @return type
     */
    public function loadStoreEntity($store_code){
        $omInstance = $this->getMagentoObjectManager();
        
        $model_store_group = $omInstance->create(\Magento\Store\Model\Group::class);
        $store_group = $model_store_group->load($store_code, 'code');
        
        return $store_group->getData();
    }
    
    public function loadCategoryEntity($category_id){
        $omInstance = $this->getMagentoObjectManager();
        
        $model_category = $omInstance->create(\Magento\Catalog\Model\Category::class);
        $ma_model_category = $model_category->loadByAttribute('entity_id', $category_id);
        
        return $ma_model_category->getData();
    }
    
    
    protected function getStoreCode(){
        $called_class = get_called_class();
        $reflector = new \ReflectionClass($called_class);
        
        return $called_class::STORE_CODE;
    }
    
    
    protected function _retrieve($store_id, $page=1, $rows=30, $ext_args=[]){
        
        $omInstance = $this->getMagentoObjectManager();
        //\Gtools\Debug::testLog(__METHOD__, $store_id, __LINE__);
        $model_product_renderlist = $omInstance->create(\Magento\Catalog\Model\ProductRenderList::class);
        $ma_search_criteria = new \Magento\Framework\Api\SearchCriteria();
        $ma_search_criteria->setCurrentPage($page);
        $ma_search_criteria->setPageSize($rows);
        $model_goods = new \Ebouti\Boutique\Model\Goods();
        
        $biz_type = $this->getBizType();
        
        $products = $model_product_renderlist->getListOfBiz($ma_search_criteria, $biz_type['id'], $store_id, 'detail');
        
        $items = $products->getItems();
        
        if(empty($items)) {
            return [];
        }
        
        foreach($items as $item){
            //\Gtools\Debug::testLog(__METHOD__, $item->getData('ebouti_good'), __LINE__);
            $pro_infos[$item->getId()] = $this->_buildProductData($item, 'list');
        }
        
        //getProductEntitiesInfo();
        return $pro_infos;
    }
    
    
    protected function _buildProductData($product){
        
        //$test = new \ReflectionClass($product);
        $omInstance = $this->getMagentoObjectManager();
        $stock_model = $omInstance->create(\Magento\CatalogInventory\Model\StockState::class);
        
        $entity_data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'type' => $product->getType(),
            'is_salable' => $product->getIsSalable(),
            'store_id' => $product->getStoreId(),
            'currency_code' => $product->getCurrencyCode(),
            'sku' => $product->getData('ebouti_good/sku'),
            'unit' => strip_tags($product->getData('ebouti_good/attr_2e20941d')),
            'stock_quantity' => $stock_model->getStockQty($product->getId()),
            'stock_status' => $product->getData('ebouti_good/quantity_and_stock_status'),
            //'ext_attributes' => $product->getExtensionAttributes(),
            'images' =>  [],//$product->getImages(),
            'resource_name' => $product->getResourceName(),
            'price_info' => $product->getPriceInfo()->getFinalPrice(),
            //'original_data' => $product->getOrigData(),
            'brief' => strip_tags($product->getData('ebouti_good/short_description')),
            //'description' => $product->getData('ebouti_good/description'),
            'medias' => [],
            'viewmode_list_style' => $this->translateListViewStyle($product->getData('ebouti_good/attr_d8feb4c1')),
            'viewmode_is_poster' => $product->getData('ebouti_good/attr_acd45bca'),
            'viewmode_recommended' => $product->getData('ebouti_good/attr_601f100d'),
            'address_key' => strip_tags($product->getData('ebouti_good/attr_08b4b13d'))
        ];
        
        //\Gtools\Debug::testLog(__METHOD__, $product->getData('ebouti_good'), __LINE__);
        $medias = $product->getMediaGallery();
        foreach($medias as $media){
            $entity_data['medias'][] = [
                'position'=>$media->getPosition(), 
                'url'=>$media->getUrl(), 
                'media_type'=>$media->getMediaType(), 
                //'emtity_id'=>$media->getEntityId()
            ];
        }
        
        $cus_attrs = $product->getCustomAttributes();
        foreach($cus_attrs as $key=>$attr){
            //\Gtools\Debug::testLog(__METHOD__, [$key, get_class($attr)], __LINE__);
        }
        
        //\Gtools\Debug::testLog(__METHOD__, $product->getData('ebouti_good'), __LINE__);
        return $entity_data;
    }
    
    
    /**
     * 
     * #Description:
     * 
     * 
     * @param type $field_value  $product->getData('ebouti_good/attr_d8feb4c1')
     */
    protected function translateListViewStyle($field_value){
        $fields = [
            5 => 'blog',
            6 => 'simple',
            8 => 'five_grids',
            7 => 'three_grids'
        ];
        
        if(array_key_exists($field_value, $fields)){
            return $fields[$field_value];
        }
        
        //\Gtools\Debug::testLog(__METHOD__, $field_value, __LINE__);
        return $fields[5];
    }
    
}