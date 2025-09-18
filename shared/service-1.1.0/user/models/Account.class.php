<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Account
 *
 * @author glools
 */
class Account {
    //put your code here
    
    
    
    public function getAccount($id){
        return $this->getOne($id);
    }
    
    
    
    
    public function tablename(){
        return 'account';
    }
}
