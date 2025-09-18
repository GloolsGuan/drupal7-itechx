<?php
/*
 * Created on Oct 17, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

Lib_Glools_Load::loadFile('glools/base', 'lib');

class Lib_Glools_Misc extends Lib_Glools_Base{
	
    
    static public function addStyle($styleFile, $vars){
    	debug_record_data(__METHOD__, array($styleFile));
    }
    
    static public function addScript($scriptFile, $vars){
    	debug_record_data(__METHOD__, array($scriptFile));
    }
}