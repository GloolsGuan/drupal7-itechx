<?php
/*
 * Created on Jan 9, 2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function _theme_lasooo_importCSS($path){
    
    if (empty($path) || !is_string($path))  {
        return '';
    }
    
    $dPath = DRUPAL_ROOT . $path;
    //debug_record_data(__FUNCTION__, file_exists($dPath) . ' : ' . $dPath);
    if (file_exists($dPath)) {
        ob_start();
        include($dPath);
        $s = ob_get_contents();
        ob_end_clean();
        return $s;
    }
    
    return '';
}

function theme_lasooo_show_breadcrumb($breadcrumb){
    if (!is_array($breadcrumb) || 1>count($breadcrumb)) {
        return null;
    }
    $re = '';
    foreach ($breadcrumb as $link) {
        $re .= empty($re) ? $link : '&nbsp;»&nbsp;'.$link;
    }
    return $re;
}