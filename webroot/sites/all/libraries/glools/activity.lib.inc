<?php

!defined(DRUPAL_ROOT) or exit("The library Lib_Glools_Activity is running for drupal 7 system.");
module_exists('glools') or exit("Before you use Lib_Glools_Activity you should enable glools module first.");

Lib_Glools_Load::loadFile('glools/view', 'lib');


class Lib_Glools_Activity extends Lib_Glools_Base{
	
    public function __construct($app=null, $aid=null){
    	
    }
    
}