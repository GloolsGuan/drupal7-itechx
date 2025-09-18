<?php
/*
 * Created on 2012-1-4
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function theme_glools_item_list($variables) {
    $items = $variables['items'];
    $title = $variables['title'];
    $type = $variables['type'];
    $attributes = $variables['attributes'];

    $cAttributes = array('class'=>'item-list');
    
    if (array_key_exists('cAttributes', $variables) 
        && is_array($variables['cAttributes'])
        && count($variables['cAttributes'])>0 ) {
        $cAttributes = array_merge($cAttributes, $variables['cAttributes']);
    }

    $output ='<div ' .drupal_attributes($cAttributes). ' >';
    if (isset($title)) {
        $output .= '<h3>' . $title . '</h3>';
    }

    if (!empty($items)) {
        $output .= "<$type" . drupal_attributes($attributes) . '>';
        $num_items = count($items);
        foreach ($items as $i => $item) {
            $attributes = array();
            $children = array();
            $data = '';
            if (is_array($item)) {
                $attributes = isset($item['attributes']) ? $item['attributes'] : array();
                $children   = isset($item['children'])   ? $item['children']   : array();
                $data       = isset($item['data'])       ? $item['data']       : ''; 
            }else {
                $data = $item;
            }
            
            if (count($children) > 0) {
                // Render nested list.
                $data .= theme_item_list(array('items' => $children, 'title' => NULL, 'type' => $type, 'attributes' => $attributes));
            }
            if ($i == 0) {
                //$attributes['class'][]  = 'first';
            }
            if ($i == $num_items - 1) {
                //$attributes['class'][] = 'last';
            }
            
            $output .= '<li' . drupal_attributes($attributes) . '>' . $data . "</li>\n";
        }
        $output .= "</$type>";
    }
    $output .= '</div>';
    return $output;
}