<?php
/**
 * Update description:
 * [0.1]  The first version of view lib.
 * [0.2]  Fixed view options parameter bug, Now you can set any options with the "view path' for specified 
 *        view like the 'views-preset.data.inc', e.g $options=array('sample/hello'=>array(...));
 *        Now, there are three item in options for every view, they are 'global', 'private', 'view', 
 *        see method loadView(). You can get the options item by '$this->options["global|private|view"]' in
 *        your view class.
 * [0.3]  Added functionality "preset" for view.
 * 
 * */
!defined(DRUPAL_ROOT) or exit("The library Lib_Glools_Mincs is running for drupal 7 system.");
module_exists('glools') or exit("Before you use Lib_Glools_View you should enable glools module first.");

Lib_Glools_Load::loadFile('glools/view', 'lib');


class Lib_Glools_View extends Lib_Glools_Base{
    
    protected $module = '';
    
    protected $presetViews = array();
    
    protected $builtData = array();
    
    protected $version = '0.3';
    
    public function __construct($options=null){
        if( !empty($options['module']) )
            $this->setModule($options['module']);
    }
    
    public function setModule($module){
        if( module_exists($module) ){
            $this->module = $module;
        }
    }
    
    
    public function buildViews($presetSection, $options=array()){
        
        if( empty($this->presetViews[$presetSection]) ){
            $this->exception('Error, there is no valid preset views for '.__METHOD__);
        }
        
        if (!isset($this->presetViews[$presetSection]) || empty($this->presetViews[$presetSection])) {
            watchdog('library:view', 'There is no valid section preset in views queue. (:module.:presetSection)', array(':module'=>$this->module, ':presetSection'=>$presetSection), WATCHDOG_ERROR);
            return $this;
        }
        
        foreach($this->presetViews[$presetSection] as $region=>$views ){
            $this->builtData[$region] = $this->loadViews($views, $options, $region);
        }
        
        return $this;
    }
    
    
    public function loadViews($views, $options=array(), $region=''){
        if( empty($views) ){
            $this->exception('Error, there is no valid views provided for '.__METHOD__);
        }
        $loaded = array();
        foreach( $views as $weight=>$info ){
            
            $loaded[$weight] = $this->loadView($info, $options, $weight, $region);
        }
        
        return $loaded;
    }
    
    
    public function loadView($info, $options=array(), $weight=0, $region=''){
        
        /**
         * when you set options for specified view danamically. your array item name should under following rule
         * 1. [region name]_[view path]_['private_key']
         * 2. [region name]_[view path]_[weight]
         * 3. [region name]_[view path] (In this way, It is means the options would be used by more view object which
         *    has the same 'view path',and more view object in the same region is allowed.)
         * */
        
        if (!isset($info['private_key'])) {
            $info['private_key'] = $info['view'].'-'.$weight;
        }
        
        $optionsKey = array(
            $region.'.'.$info['view'].'.'.$info['private_key'],
            $region.'.'.$info['view'].'.'.$weight,
            $region.'.'.$info['view'],
            $info['view']
        );
        
        $privateOptions = null;
        
        foreach ($optionsKey as $key) {
            if (!isset($options[$key])){
                continue;
            }
            $privateOptions = $options[$key];
        }
        if (null==$privateOptions) {
        	$privateOptions = $this->loadPresetData($info, $this->module);
        }
        
        $o = array(
            'view' => $info,
            'global'=> isset($options['global']) ? $options['global'] : null,
            'private'=> $privateOptions
        );
        $objView = Lib_Glools_Load::load($info['view'], 'view', $o);

        if( false == $objView ){
            return $objView ;
        }
        
        return $objView->fetchView();
    }
    
    
    public function loadPreset(){
        if( empty($this->module) ){
            $this->exception('Error, Before you run Lib_Glools_View::loadPreset, you should set module parameter for the class.');
        }
        
        $presetFile = drupal_get_path('module', $this->module).'/views-preset.data.inc';
        
        if( !file_exists($presetFile) ){
            $this->exception('Error, there is no valid preset file for '.__METHOD__);
        }
        
        $this->presetViews = include($presetFile);
        
        if( empty($this->presetViews) || !is_array($this->presetViews) ){
            $this->exception('Error, there is no valid preset file for '.__METHOD__);
        }
        return $this;
    }
    
    public function attachToPage(&$vars){
        
        foreach($this->builtData as $region=>$elements){
            
            if( !isset($vars[$region]) ){
                $vars[$region] = $elements;
            }else{
                
                foreach($elements as $element){
                    $vars[$region][] = $element;
                }
            }
        }
    }
    
    protected function loadPresetData($view, $module){
    	static $objViewPreset;
        
        if(!$objViewPreset instanceof Lib_Glools_View_Preset) {
        	$objViewPreset = Lib_Glools_Load::load('glools/view/preset', 'lib');
        }
        
        return $objViewPreset->load($view, $module);
    }
}