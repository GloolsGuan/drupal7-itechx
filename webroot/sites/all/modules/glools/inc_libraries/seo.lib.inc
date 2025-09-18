<?php

/**
  * set seo
  * $data = array('title' => ,'keywords' =>, 'description' => );
  *
  */

function set_seo($data = array()) {
    if($data['description']) {
        $meta_description = array(
            '#type' => 'html_tag',
            '#tag' => 'meta',
            '#attributes' => array(
                'name' => 'description',
                'content' => $data['description'],
            )
        );

        drupal_add_html_head($meta_description,'meta_description');
    }
  
    if($data['keywords']) {
        $meta_keywords = array(
            '#type' => 'html_tag',
            '#tag' => 'meta',
            '#attributes' => array(
                'name' => 'keywords',
                'content' => $data['keywords'],
            )
        );
        drupal_add_html_head($meta_keywords,'meta_keywords');

    }
    
    $qqCheck = array(
        '#type' => 'html_tag',
        '#tag'  => 'meta',
        '#attributes' => array(
            'name' => 'qc:admins',
            'content' => '354473327764137776375'
        )
    );
    
    drupal_add_html_head($qqCheck, 'meta_qc:admins');
    
}
