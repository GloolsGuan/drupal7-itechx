<?php
namespace Ebouti\Boutique\Model;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Goods extends \Gtools\Base\Model{
    
    
    
    protected $raw_entity = [];

    
    public function create(){
        
    }
    
    
    public function loadGoodIdsInStore($category_id){
        $stm = self::$mpdo->prepare('select product_id from ma_catalog_category_product where category_id=:category_id;');
        $stm->execute([
            ':category_id' => $category_id
        ]);
        
        if($stm->rowCount()>0){
            return $stm->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        return [];
    }
    
    
    public function getEntityData(){
        return $this->getAttributes(null, ['openid']);
    }
    
    public static function tableName(){
        return '{{ma_catalog_product_entity}}';
    }
    
    
}
