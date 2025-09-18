<?php
namespace Gtools\Events\Module;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class InitializeRequest extends \Gtools\Yii\Event{
    
    
    public $request = null;
    
    public $controller = null;
    
    private $hasAccess = false;
    
    
    
}