<?php
namespace service\base;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\base\Module as YiiModule;
use yii\base\ViewContextInterface;

require_once(DIR_SHARED . '/../global.conf.php');

class Module extends YiiModule
{

    const EVENT_INIT = 'module.init';
    protected $base_name = '';

    /**
     * Running environment
     *  APACHE virtual host settings by SetEnv directive
     *  SetEnv APPLICATION_ENV "development" | "test" | "work"
     * @var type
     */
    protected $_env = null;

    /**
     * Module parameters located in [MODULE]/includes/params.conf.php
     * @var type
     */
    protected static $conf = null;

    /**
     *If supported private entity, If yes, It is means that you can register private entity by third part module.
     *
     * If the module supported private entity, The unique_id is required in your main entity db table.
     *
     * Addtional fields if private entity supported:
     *  - unique_id char(64) not null default ''
     *  - owner char(64) not null default ''
     *
     * @var type
     */
    public static $is_supported_private_entity = false;


    public function __construct($id = '', $parent = null, $config = [])
    {
        $class_name = get_class($this);
        $this->base_name = substr($class_name, 0, strrpos($class_name, '\\'));

        if (isset($_SERVER['APPLICATION_ENV']) && in_array($_SERVER['APPLICATION_ENV'], ['development', 'test', 'work'])) {
            $this->_env = $_SERVER['APPLICATION_ENV'];
        } else {
            $this->_env = 'work';
        }

        if (empty($id)) {
            $id = get_class($this);
        }

        parent::__construct($id, $parent, $config);

        $this->on(self::EVENT_INIT, [$this, 'onInit']);
        $this->trigger(self::EVENT_INIT);
    }


    public function behaviors(){
        return [
            'error_handler' => [
                'class' => "\service\base\behaviors\Error",
                'auto_log' => true,
                'log_level' => 'exception'
            ]
        ];
    }


    public function onInit(\yii\base\Event $e){
        ;
    }

    /**
     * @TODO checking service version
     * @param type $version
     * @return type
     */
    public function setVersion($version)
    {
        //\Yii::warning(sprintf('[Version not supported] The service %s version does not match with the required service version', get_class($this), get_class($this)));
        return $version;
    }


    /**
     *
     */
    public function isSupportPrivateEntity()
    {
        return $this->is_supported_private_entity;
    }


    public function getUniqueId()
    {
        return $this->base_name;
    }


    public function getCsrfToken()
    {
        $request = \Yii::$app->getRequest();

        //-- Force CSRF verify by session --
        $request->enableCsrfCookie = false;

        $csrf_token = array('field_name' => $request->csrfParam, 'field_value' => $request->getCsrfToken());

        return $csrf_token;
    }


    public function buildCsrfCookie()
    {
        return array('field_name' => \Yii::$app->request->csrfParam, 'field_value' => \Yii::$app->request->generateCsrfToken());
    }


    /**
     * Description:
     *
     * Response data structure:
     * - status (string) success:Program executed successfully,
     *                   error: Provided parameters error.
     *                   failed: If exception happened.
     * - code   (int) 200 -599,
     *                200 - 299 for success, Generally, 200 for data=null, 201 data=(not empty)
     *                400 - 499 For client error, e.g. Provided invalid parameters.
     *                500 - 599 For system error.
     * - data   (multiple data)
     *
     *
     * @param type $status
     * @param type $code
     * @param type $data
     * @return type
     */
    public function buildResponse($status, $code, $data)
    {
        return array(
            'status' => $status,
            'code' => $code,
            'data' => $data
        );
    }

    /**
     * @param $response
     * @return bool
     */
    public function isErrorResponse($response)
    {
        if (is_array($response) && isset($response['code']) && $response['code'] != 200 && $response['code'] != 201) return true;
        return false;
    }


    /**
     * Load module/components
     *
     * @return type
     */
    public function loadComs()
    {
        $base_name = substr(0, strrpos('/'), get_class($this));
        return [];
    }


    public function loadComInfos()
    {
        // Component register information
        return [];
    }

    public function loadBaseEntity($entity_id){
        throw new Exception(sprintf('System error: The method %s is not exist in the module %s.', __METHOD__, __CLASS__), 501);
    }


    public function loadCom($name, $base_entity=null)
    {
        $com_ns = $this->getComsNS() . '\\' . str_replace('/', '\\', ucfirst($name));

        $params = [
            $com_ns,
            $this,
            ['base_entity'=>$base_entity, 'module'=>$this]
        ];


        $service_com = \Yii::createObject($com_ns,  $params);

        //\GtoolsDebug::testLog(__METHOD__, $service_com);
        return $service_com;
    }


    public function getServiceNS()
    {
        $this_ns = get_class($this);
        return substr($this_ns, 0, strrpos($this_ns, '\\'));
    }


    public function getComsNS()
    {
        return $this->getServiceNS() . '\\coms';
    }

    public function getModelsNS()
    {
        return $this->getServiceNS() . '\\models';
    }


    /**
     * You can load system configuration parameters by x_path format, It is easy to load
     *
     * split parameter names by '/', That is all what you need to do.
     *
     * @param type $x_name
     */
    public function loadParams($x_name, &$conf_param = null, $env = null)
    {
        if (!empty($env)) {
            $x_name = sprintf('%s/%s', $x_name, $env);
        }
        //\GtoolsDebug::testLog(__FILE__, 'TODO: Remove the code from controller.', __METHOD__);
        return \Yii::$app->get('params')->load($x_name, $conf_param);
    }


    /**
     * @deprecated
     * @param $func_source_data
     * @param $func_slave_data
     * @param array $field_mapping ['master_table_field'=>'slave_table_field']
     * @param string $append_name
     * @return mixed
     */
    public function mergeAsyncData($func_source_data, $func_slave_data, array $field_mapping = [], $append_name = 'sub')
    {
        $master_field = array_keys($field_mapping)[0];
        $slave_field = array_values($field_mapping)[0];

        $err = $source_data = $func_source_data();
        if ($this->isErrorResponse($err)) return $this->buildResponse($err['status'], $err['code'], $err['data']);
        empty($source_data) && $source_data = [];
        $source_ids = array_column($source_data, $master_field);

        $err = $slave_data = $func_slave_data($source_ids);
        if ($this->isErrorResponse($err)) return $this->buildResponse($err['status'], $err['code'], $err['data']);
        empty($slave_data) && $slave_data = [];
        $slave_ids = array_column($slave_data, $slave_field);

        foreach ($source_data as &$item) {
            if (false === ($index = array_search($item[$master_field], $slave_ids))) continue;
            $item[$append_name] = $slave_data[$index];
        }
        return $source_data;
    }
}