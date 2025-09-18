<?php
namespace service\weixin\handlers;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Yii;
use \frontend\components\Component as ComComponent;
use \frontend\components\weixin\Message as ComWeixinMessage;


class Message extends ComComponent
{
    protected $com_wx_message = null;
    
    protected $receiver = null;
    
    public function __construct($config=[]){
        
        parent::__construct($config);
    }
    
    
    
    
    
    public function process($receiver){
        $this->receiver = $receiver;
        $com_wx_message = new ComWeixinMessage($receiver);
        
        $search_text = $this->search($receiver['Content']);
        
        return $com_wx_message->build('text', ['content'=>$search_text], $receiver);
    }
    
    
    public function search($query){
        return 'Hello,world!';
    }
}