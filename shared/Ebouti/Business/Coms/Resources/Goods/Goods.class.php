<?php
namespace Ebouti\Business\Coms\Resources\Goods;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Ebouti\Business\Libs\Abstracts\BizComponent;
class Goods extends BizComponent{
    
    public $biz_type = 'goods';
    
    public function info(){
        return [
            'title' => '',
            'brief' => ''
        ];
    }
    
    
    public function schema(){
        return include(dirname(__FILE__) . '/goods.bundle.schema');
    }
    
    public function getBizType(){
        return $this->biz_type;
    }
    
}