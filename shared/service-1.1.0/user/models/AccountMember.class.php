<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Member
 *
 * @author glools
 */
class Member extends Account{
    //put your code here
    
    protected $parent_model = null;
    
    public function tablename(){
        return 'account_member';
    }
}
