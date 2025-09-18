<?php
/*
 * Created on Mar 22, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function member_user_register_form_alter(&$form, &$form_state, $form_id){
    
    //$account = $form['account'];
    //unset($form['account']);
    
    $markup = Glools_Inc_Tools::renderTpl(array(), 'register_member_description', 'member');
    
    /*
    $form['description'] = array(
        '#markup' => $markup,
        '#weight' => 1000
    );
    */
    
    $form['#description'] = $markup;
    
    $form['#title'] = t('Welcome you to be a member of Lasooo.');
    $form['#theme_wrappers'] = array('glools_form_wrapper');
    
    
    //array_unshift($form, array('description'=>array('#markup'=>$markup, '#weight'=>1000)));
    
    if (!isset($form['#after_build'])) {
        $form['#after_build'] = array();
    }
    $form['#after_build'][] = 'member_form_afterBuild';
}

function member_user_register_form_afterBuild(&$form, &$form_state){
	
    drupal_add_js(path_to_theme() . '/js/blocks/user-register.block.js');
    drupal_add_css(path_to_theme() . '/css/blocks/user-register.block.css');
    
    return $form;
}