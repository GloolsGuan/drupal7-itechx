<?php
namespace service\community\coms;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class Like extends \service\base\Component{
    
    public function __construct($id = '', $parent = null, $config = array()) {
        parent::__construct($id, $parent, $config);
    }
    
    public function init(){
        //\GtoolsDebug::testLog(__METHOD__, [$this->base_entity, $this->base_entity_id]);
        if (empty($this->base_entity) || !is_numeric($this->base_entity['id'])) {
            if (is_numeric($this->base_entity_id)) {
                //\GtoolsDebug::testLog(__METHOD__, $this->module);
                $base_entity_result = $this->module->loadBaseEntity($this->base_entity_id);
                if (201!==$base_entity_result['code']) {
                    throw new \Exception('System error: Failed to load service component \service\community\coms\Forum, Parameter "base_entity" is invalid.', 500);
                }
                $this->base_entity = $base_entity_result['data'];
            }
        }
    }
    
    
    /**
     * Mark or unmark 
     * 
     * @param numeric $user_id User
     * 
     */
    public function mark($uc_user_code, $object, $object_id, $like_type){
        $model_like = new \service\community\models\Like();
        
        if (!is_string($object)) {
            return $this->buildResponse('error', 403, 'Invalid parameter "object" should be string.');
        }
        
        if(!is_numeric($object_id)) {
            return $this->buildResponse('error', 401, 'Invalid parameter object_id, It should be numeric.');
        }
        
        if (!is_string($uc_user_code) || 64!==strlen($uc_user_code)) {
            return $this->buildResponse('error', 402, 'Invalid parameter user_uc_code.');
        }
        
        if (!is_string($like_type) || !in_array($like_type, ['thumbs_up', 'collecting'])) {
            return $this->buildResponse('error', 403, 'Invalid parameter like_type should be string.');
        }
        
        $existsLike = $model_like->find()->where([
            'like_object' => $object,
            'object_id' => $object_id,
            'member_uc_code' => $uc_user_code,
            'like_type' => $like_type
        ])->one();
        /*
        \GtoolsDebug::testLog(__METHOD__, [
            $existsLike, $uc_user_code, $object, $object_id, $like_type
        ]);*/
        $action='';
        if (null!==$existsLike) {
            $existsLike->delete();
            $action='unmark';
        } else {
            $re = $model_like->setAttributes([
                'like_object' => $object,
                'object_id' => $object_id,
                'member_uc_code' => $uc_user_code,
                'like_type' => $like_type
           ])->insert();
            $action = 'mark';
        }
        
        $total = $this->totals($object, $object_id, $like_type);
        
        return $this->buildResponse('success', 201, ['action'=>$action, 'total'=>$total]);
    }
    
    
    public function getAllOnObject($uc_user_code, $object, $object_id){
        $model_like = new \service\community\models\Like();
        $all_result = $model_like->find()->select('count(id) as total, like_type')->where([
            'like_object' => $object,
            'object_id' => $object_id
        ])->groupBy('like_type')->asArray()->indexBy('like_type')->all();
        
        $user_result = $model_like->find()->where([
            'member_uc_code' => $uc_user_code, 
            'like_object' => $object,
            'object_id' => $object_id
        ])->asArray()->indexBy('like_type')->all();
        
        //\GtoolsDebug::testLog(__METHOD__, [$all_result, $user_result]);
        
        return $this->buildResponse('success', 201, [
            'all' => $all_result,
            'user' => $user_result
        ]);
    }
    
    
    protected function totals($object, $object_id, $like_type){
        $model_like = new \service\community\models\Like();
        $totalLike = $model_like->find()->select('count(id) as total')->where([
            'like_object' => $object,
            'object_id' => $object_id,
            'like_type' => $like_type
        ])->asArray()->one();
        //\GtoolsDebug::testLog(__METHOD__, $totalLike);
        return $totalLike['total'];
    }

}