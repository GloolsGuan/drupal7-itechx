<?php
/*
 * Created on 2011-3-23
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 /**
  * HOOK: hook_block_info
  * */
 
 define('BLOCKER_BLOCK_HOOK', 'blocker');
 
 function blocker_block_info(){
    //static $loadedBlocks ;
    $hook = 'blocker';
    $blocks = array();
    $loadedBlocks = _blockerLoadBLocks();
    foreach($loadedBlocks as $block){
        $delta = $hook.'_block_'.$block;
    	$blockInfoHook =$delta.'_info';
        $blocks[$block] = $blockInfoHook();
    }
    return $blocks;
 }
 
 /**
  * HOOK：hook_block_view
  * */
function blocker_block_view($delta){
    //echo $delta.'<br />';
	
    $hook = BLOCKER_BLOCK_HOOK.'_block_'.$delta.'_view';
    
    if(!_blockerLoadBlock($delta) || !function_exists($hook)){
        return null;
    }
    
    //echo $hook.'; ';
    return call_user_func($hook, array($delta));
}

 
 /**
  * Internal function group
  * */
 
 /**
  * Load single block
  * */
 function _blockerLoadBlock($block){
 	$blocks = array();
    
    if(!empty($blocks) && in_array($block, $blocks)){
        return true;
    }
    
    $blockFile = BLOCKER_DIR_BLOCKS.'/'.$block.'.block.php';
    
    if(file_exists($blockFile)){
    	include_once($blockFile);
        $blocks[$block] = $blockFile;
        return true;
    }
    
    return false;
 }
 /**
  * Scan block folder and load all block files.
  * @return block names
  * */
 function _blockerLoadBlocks($block = null){
    $blocks = array();
    
    $paths = glob(BLOCKER_DIR_BLOCKS.'/*.block.php');
    
    foreach($paths as $path){
        require_once($path);
        $blocks[] = basename($path,'.block.php');
    }
    
    return $blocks;
 }
