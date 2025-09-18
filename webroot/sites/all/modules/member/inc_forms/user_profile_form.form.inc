<?php
/*
 * Created on Jun 21, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function member_user_profile_form_alter(&$form, &$form_state, $form_id){
	
    if (!isset($form['#after_build'])) {
        $form['#after_build'] = array();
    }
    
    $form['navigation'] = array(
        '#weight'   => -100,
        '#children' => theme_glools_item_list(
                           array('title'=>t('Account Settings'), 
                                 'type'=>'ul', 
                                 'items'=>member_user_profile_form_navigator(),
                                 'cAttributes' => array(
                                     'class' => array('account-settings-navigation')
                                 )
                           )
                       )
    );
    
    $form['account']['field_telephone']= $form['field_telephone'];
    unset($form['field_telephone']);
    $form['account']['field_telephone']['#weight'] = 0;
    
    $form['#after_build'][] = 'member_form_afterBuild';
    
    //debug_record_data(__FUNCTION__, array_keys($form));
}

function member_user_profile_form_afterBuild($form, &$form_state){
	
    drupal_add_css(path_to_theme() . '/css/components/user_profile_form.com.css');
    drupal_add_js(path_to_theme() . '/js/components/user_profile_form.com.js');
    
    return $form;
}

function member_user_profile_form_navigator(){
	return array(
        'basic'   => array(
            'data' => t('Basic Information'),
            'attributes' => array('class'=>'userprofile basic', 'id'=>'show:edit-account')
        ),
        /*
        'passwd'  => array(
            'data' => t('Setting password'),
            'attributes' => array('class'=>'userprofile passwd')
        ),
        */
        'photo'   => array(
            'data' => t('Personal Photo'),
            'attributes' => array('class'=>array('userprofile photo'), 'id'=>'show:edit-picture')
        ),
        'signature' => array(
            'data' => t('Signature'),
            'attributes' => array('class'=>array('userprofile signature'), 'id'=>'show:edit-signature-settings')
        ),
        'location'  => array(
            'data' => t('Location'),
            'attributes' => array('class'=>'userprofile location', 'id'=>'show:edit-field-location-area')
        ),
        /*'focus-area' => array(
            'data' => t('Focus Area'),
            'attributes' => array('class'=>array('userprofile focus-area'), 'id'=>'show:edit-field-focus-area')
        ),
        'personal-authentication' => array(
            'data' => t('Personal Authentication'),
            'attributes' => array('class'=>array('userprofile personal-authentication'), 'id'=>'show:pa')
        ),
        'team-authentication'     => array(
            'data' => t('Team Authentication'),
            'attributes' => array('class'=>array('userprofile team-authentication'), 'id'=>'show:ta')
        ),
        'company-authentication' => array(
            'data' => t('Company Authentication'),
            'attributes' => array('class'=>array('userprofile company-authentication'), 'id'=>'show:ca')
        ),
        'cooperator-authentication' => array(
            'data' => t('Cooperator Authentication'),
            'attributes' => array('class'=>array('userprofile cooperator-authentication'), 'id'=>'show:coa')
        )*/
    );
}