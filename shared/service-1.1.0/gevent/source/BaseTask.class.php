<?php
/* 
 * Notice: 
 * 1. DON'T DECLARE NAMESPACE, IT WILL PUT OFF CONSOLE INVOKE.
 * 2. The original behavior class is located in \service\gevent\source\Behavior, all of Event.Behavior class
 *    should keeping the same with the original behavior. but you can copy them to your local application.
 * 3. The class name of Behavior should be [Application_tag]_[Module_name]_[Behavior_name]_Behavior, This is
 *    the original php class name rule, and It is best way to across system in the same platform.
 * 4. Behavior will be invoke intime or  in console format.
 * 5. The another name of your behavior is "task handler", It is means that the behavior is self-exacutable. 
 * 6. When you declare an XXXTask in your module's local folder, you should specified the path ot BaseTask
 *
 * Some documents:
 * 1. http://pear.php.net/package/Console_CommandLine/docs/latest/Console_CommandLine/Console_CommandLine.html
 *
 */
require_once(__DIR__ . '/../../../../global.conf.php');
require_once(sprintf('%s/vendor/autoload.php', GlobalConfig::get('dirs/shared') . '/' . GlobalConfig::get('yii_version')));
require_once(sprintf('%s/vendor/yiisoft/yii2/Yii.php', GlobalConfig::get('dirs/shared') . '/' . GlobalConfig::get('yii_version')));

 class BaseTask extends \yii\base\Behavior{
    
    protected $version = 0.1;
    protected $original_location = '[shared]/service-[service_version]/events/source/BaseTask';
    protected $file_md5 = '';
    
    
    
    /**
     * The main method used for support console format invoke as an "task handler"
     */
    public function main(){}
    
}


/**
 * Parse command line arguments
 * 
 * @see http://php.net/manual/en/features.commandline.php
 * @param type $args
 * @return type
 */
if (!function_exists('basetask_arguments')) {
    function basetask_arguments ( $args ){
        array_shift( $args );
        $args = join( $args, ' ' );
        preg_match_all('/ (--\w+ (?:[= ] [^-]+ [^\s-] )? ) | (-\w+) | (\w+) /x', $args, $match );
        $args = array_shift( $match );
        $ret = array( 'input'    => array(),  'commands' => array(), 'flags'    => array());

        foreach ( $args as $arg ) {
            // Is it a command? (prefixed with --)
            if ( substr( $arg, 0, 2 ) === '--' ) {
                $value = preg_split( '/[= ]/', $arg, 2 );
                $com   = substr( array_shift($value), 2 );
                $value = join($value);

                $ret['commands'][$com] = !empty($value) ? $value : true;
                continue;
            }

            // Is it a flag? (prefixed with -)
            if ( substr( $arg, 0, 1 ) === '-' ) {
                $ret['flags'][] = substr( $arg, 1 );
                continue;
            }
            $ret['input'][] = $arg;
            continue;
        }

        return $ret;
    }
}


/**
 * Get current task class name for process the task
 * 
 * Notice: your file name should be the same with the task class name.
 */
if (!function_exists('basetask_getTaskClassName')){
    function basetask_getTaskClassName(){
        $path_info = pathinfo(__FILE__);
        $base_name = $path_info['basename'];
        $class_name = substr($base_name, 0, strpos($base_name, '.'));
        return $class_name;
    }
}


/**
 * Process task on asynchronous format
 * 
 * Your task script may be executed by an python script or php script, The "main" method will be the entry of the task handler.
 * 
 */
$_argvs = basetask_arguments ( $argv );
if (!empty($_argvs) && array_key_exists('task_id', $_argvs['commands'])){
    try{
        $task_class = basetask_getTaskClassName();
        if ('BaseTask'==$task_class){
            //throw new Exception('System error, you can not build an task with name "BaseTask".');
        }
        
        $task_instance = new $task_class();
        return $task_instance->main($_argvs['commands']);
        
    }catch(Exception $e){
        echo $e->getMEssage();
        return -1;
    }
}

