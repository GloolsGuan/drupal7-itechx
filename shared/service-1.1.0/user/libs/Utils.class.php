<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Utils extends \service\base\Base{
    
    
    /**
     * Build user unique_code by login name and field information.
     * 
     * The unique_code the alise of user_id, build by login_name, It is maybe an mobile,
     * or an string.
     * It is invalid for verify whether the user is valid.
     */
    public static function buildCode($login_name){
        
        
        
        return hash('sha256', $login_name);
    }
}