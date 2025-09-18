<?php
namespace service\base;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Component
 *
 * @author glools
 */
class Component extends Module {
    //put your code here
    
    protected $base_entity = null;
    protected $base_entity_id = null;
    
    public function __construct($id = '', $parent = null, $config = array()) {
        //\GtoolsDebug::testLog(__METHOD__, [$id, $parent, $config]);
        parent::__construct($id, $parent, $config);
    }
    
    
    public function setBase_entity($data){
        //\GtoolsDebug::testLog(__METHOD__, ['xxxx', $data, get_class($this->module)]);
        
        if (is_numeric($data)) {
            $this->base_entity_id = $data;
            return ;
        }
        
        if (is_array($data) && (!array_key_exists('id', $data) || !is_numeric($data['id']))) {
            throw new \Exception('System error: Failed to set base entity, the primary key name must be "id" and the value must be numeric.', 500);
        }
        
        $this->base_entity = $data;
    }
}
