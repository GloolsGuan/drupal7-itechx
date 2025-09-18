<?php
namespace \Gtools\SmartModel\Coms\Fields\Address;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Gtools\SmartModel\Libs\AbstractField;

class Address extends AbstractField{
    
    
    public function beforeBuild() {
        if (empty($this->default_value) || !is_string($this->default_value)){
            $this->default_value = array(
                'province' => '', 
                'city' => '',
                'area' => '',
                'detail' => ''
            );
            return;
        }
        
        $original_value = $this->default_value;
        list($path, $detail) = explode('/', $original_value);
        list($province, $city, $area) = explode(',', $path);
        
        $this->default_value = array(
            'province' => $province, 
            'city' => $city,
            'area' => $area,
            'detail' => $detail
        );
        
        //Lib_Gtools_Debug::testLog(__FILE__, array($this->label, serialize($this->values)), __METHOD__);
    }
    
    
    /**
     * $value = array(
     *     'provice' => '',
     *     'city' => '',
     *     'area' => '',
     *     'detail' => ''
     * );
     * @param type $value
     */
    public function buildForStorage($value){
        
        if (!is_array($value)) {
            return '';
        }
        
        
        
        $detail = $value['detail'];
        unset($value['detail']);
        $vs = array_values($value);
        $path = implode(',', $vs);
        
        //Lib_Gtools_Debug::testLog(__FILE__, array($value, $detail, $path), __METHOD__);
        
        return sprintf('%s/%s', $path, $detail);
    }
}