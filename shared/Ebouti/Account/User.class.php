<?php
namespace Ebouti\Account;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class User extends \Gtools\Yii\Module{
    
    
    
    
    public function loadByOpenId($openid){
        $existed_user = Model\User::findOne(['openid'=>$openid]);
        //\Gtools\Debug::testLog(__METHOD__, $existed_user->getAttributes(), __LINE__);
        if (!empty($existed_user)){
            return $this->buildResponse(201, 'success', $existed_user);
        }
        
        return $this->buildResponse(200, 'success');
    }
    
    
    public function createWxAccount($wx_user_state, &$ma_object_manager){
        $model_user = new Model\User();
        
        $ebouti_user = [];
        array_walk($wx_user_state, function($value, $key) use(&$ebouti_user) {
            $key = strtolower($key);
            $ebouti_user[$key] = $value;
        });

        //-- Try to register an new ebouti user or update ebouti user information base on Weixin user information.
        $existed_user = Model\User::findOne(['openid'=>$ebouti_user['openid']]);
        if(null==$existed_user){
            $model_user->setAttributes($ebouti_user, false);
            $model_user->save();
        }else{
            //$ebouti_user['ma_customer_id'] = time();
            $existed_user->setAttributes($ebouti_user, false);
            $existed_user->update();
        }
        
        $wx_user = empty($existed_user) ? $model_user : $existed_user;
        
        //-- Register new user in magento system --
        $ma_register_result = $this->registerInMagento($wx_user->getAttributes(), $ma_object_manager);
        if (201==$ma_register_result['code']){
            $ma_user_id = $ma_register_result['data']['entity_id'];
            $wx_user->setAttribute('ma_customer_id', $ma_user_id);
            $wx_user->update();
        }
        $wx_user->refresh();
        //\Gtools\Debug::testLog(__METHOD__, $wx_user->getAttributes(), __LINE__);
        return $this->buildResponse(201, 'success', $wx_user->getAttributes(null, ['openid', 'session_key']));
    }
    
    
    protected function registerInMagento($wx_user_entity, $ma_object_manager){
        $ma_model_accountmanagement = $ma_object_manager->create(\Magento\Customer\Model\AccountManagement::class);
        $ma_model_customer = $ma_object_manager->create(\Magento\Customer\Model\Customer::class);
        $ma_data_customer = $ma_object_manager->create(\Magento\Customer\Model\Data\Customer::class);
        $entity_email = $this->buildEmailByOpenId($wx_user_entity['openid']);
        
        $ma_data_map = [
            'setEmail' => $entity_email,
            'setStoreId' => $this->getStoreId(),
            'setUpdatedAt' => time(),
            'setGender' => $wx_user_entity['gender'],
            'setFirstname' => $this->getUserName($wx_user_entity['nickname']),
            'setMiddlename' => '',
            'setLastname' => $this->getUserName($wx_user_entity['nickname']),
            'setWebsiteId' => $this->getWebsiteId(),
            //'setConfirmation' => '',
            //'setPrefix' => '',
            //'setSuffix' => '',
            'setGroupId' => $this->getGroupId()
        ];
        
        foreach($ma_data_map as $k=>$v){
            $ma_data_customer->$k($v);
        }
        
        $customer = $ma_model_customer->setWebsiteId(1)->loadByEmail($entity_email);
        if(!empty($customer)){
            $create_result = $ma_model_customer->updateData($ma_data_customer);
        }else{
            $create_result = $ma_model_accountmanagement->createAccount($ma_data_customer, $this->buildPasswordByOpenid($wx_user_entity['openid']));
        }
        
        //\Gtools\Debug::testLog(__METHOD__, [get_class($customer), $create_result->getData()], __LINE__);
        
        return $this->buildResponse(201, 'success', $create_result->getData());
    }
    
    public function buildEmailByOpenId($openid){
        
        $email_key = hash('sha256', $openid );
        
        return sprintf('%ss@lasooo.com', substr($email_key, 0, 16));
    }
    
    public function getStoreId(){
        
    } 
    
    
    public function getWebsiteId(){
        
    }
    
    
    public function getGroupId($from='', $delta=''){
        ;
    }
    
    
    public function getUserName($nick_name){
        return $nick_name;
    }
    
    
    public function buildPasswordByOpenid($openid){
        
        $raw_salt = hash('sha256', $openid);
        
        $first_salt = substr($raw_salt, 0, 16);
        $middle_salt = substr($raw_salt, 16, 32);
        $last_salt = substr($raw_salt, 32, 64);
        
        return sprintf('%s*%s.%s', $first_salt, strtoupper($middle_salt), $last_salt);
    }
    
}