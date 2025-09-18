<?php
namespace service\base;
/*
 * The base service provider of YII2
 */
use yii\helpers\ArrayHelper;
/**
 * Description of Base
 *
 * @author glools
 */
class Base extends \yii\di\ServiceLocator{
    //put your code here

    protected static $params = null;

    protected static $paramsHandler = null;

    protected static $serviceLocator = null;


    public function __construct($config=[]){

        if(!file_exists(dirname(__FILE__) . '/includes/main.conf.php')) {
            throw new exception('Main configuration file should be exists.');
        }

        self::$params = include(dirname(__FILE__) . '/includes/main.conf.php');

        //合并本地配置
        $config_local=[];
        $config_local_file = dirname(__FILE__) . '/includes/config-local.php';
       if( file_exists($config_local_file))
            $config_local = include $config_local_file;
        self::$params =ArrayHelper::merge(self::$params,$config_local);

        //-- Register public YII2-core components
        if (!empty(self::$params['components'])) {
            $this->setComponents(self::$params['components']);
        }

        parent::__construct($config);
    }


    public static function getCom($id){
        self::$serviceLocator =  new Base();

        return self::$serviceLocator->get($id);
    }


    /**
     * You can load system configuration parameters by x_path format, It is easy to load
     *
     * split parameter names by '/', That is all what you need to do.
     *
     * @param type $x_name
     */
    public function loadParams($x_name, &$conf_param = null, $env=null)
    {
        if(!empty($env)) {
            $x_name = sprintf('%s/%s', $x_name, $env);
        }
        //\GtoolsDebug::testLog(__FILE__, 'TODO: Remove the code from controller.', __METHOD__);

        $conf_param = empty($conf_param) ? self::$params : $conf_param;

        return (new Params())->load($x_name, $conf_param);
    }


    public static function buildUniqueCode($login_name){
        return hash('sha256', $login_name);
    }


    /**
     *
     * @param type $name
     * @param type $args
     */
    public static function loadService($service_ns, $args=[], $parent=null){

        $definition['class'] = $service_ns;
        if (is_array($args) && !empty($args)) {
            $definition = array_merge_recursive($args, $definition);
        }
        //\GtoolsDebug::testLog(__FILE__, [$args, $definition], __METHOD__);

        return \Yii::createObject($definition, [$service_ns, $parent]);
    }
}
