<?php
namespace Saas;

/**
 * SAAS/Client
 * 
 * @Author: GloolsGuan
 * 
 * - - - - - -
 * Description
 * - - - - - -
 * 
 * #### Supporting multiple client account ####
 * **Enterprise deloying** <br /> 
 * Standalone deploying for enterprise base on second level domain with the same database system.
 * Master and slaver database structure will be required for optimizing data system later.
 * 
 * **SAAS deloying for public** <br />
 * Standalone client deploying with standalone system.
 */


class Client extends \Gtools\Yii\Module{
    
    public static function initClient(\Gtools\Saas\ClientMode $mode){
        
    }
    
    public static function buildMode($mode_name='public_saas', $options){
        
        $mode = ('enterprise'==strtolower($mode_name)) ? 'enterprise' : 'public_saas';
        
        $client_mode = new \Gtools\Saas\ClientMode($options);
        $client_mode->name = $mode;
    }
    
    
}


