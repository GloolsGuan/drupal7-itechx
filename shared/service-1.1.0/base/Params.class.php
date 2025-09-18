<?php
namespace service\base;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



class Params extends Component{
    
    
    public function loadOnEnvs($x_name, &$conf_param=null){
        $envs = $this->getEnvs();
        if (!empty($envs)) {
            $x_name = sprintf('%s/%s', $x_name, $envs);
        }
        //\GtoolsDebug::testLog(__FILE__, 'TODO: Remove the code from controller.', __METHOD__);
        return \Yii::$app->get('params')->load($x_name, $conf_param);
    }
    
    /**
     * You can load system configuration parameters by x_path format, It is easy to load
     * 
     * split parameter names by '/', That is all what you need to do.
     * 
     * @param type $x_name
     */
    public function load($x_name, &$conf_param=null){

        if (!is_string($x_name)) {
            return null;
        }
        
        $conf_param = empty($conf_param) ? \Yii::$app->params : $conf_param;
        
        if ('/'==$x_name{0}) {
            $x_name = substr($x_name, 1);
        }
        $x_args = explode('/', $x_name, 2);
        
        //\GtoolsDebug::testLog(__FILE__, [$x_name, $x_args, array_key_exists($x_args[0], $conf_param)], __METHOD__);
        if (!array_key_exists($x_args[0], $conf_param)) {
            return null;
        }
        
        if (count($x_args)>1) {
            return $this->load($x_args[1], $conf_param[$x_args[0]]);
        }
        
        return $conf_param[$x_args[0]];
    }
    
    public function getEnvs(){
        if (isset($_SERVER['APPLICATION_ENV']) && in_array($_SERVER['APPLICATION_ENV'], ['development', 'test', 'work'])) {
            $this->_env = $_SERVER['APPLICATION_ENV'];
        } else {
            $this->_env = 'work';
        }
    }
}