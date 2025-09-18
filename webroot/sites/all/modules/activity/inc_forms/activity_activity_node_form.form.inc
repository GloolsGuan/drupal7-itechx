<?php

function activity_activity_node_form_alter(&$form, $form_state, $form_id){
	/*
    $form['main_photos'] = array(
        '#type'  => 'fieldset',
        //'#title' => t('活动图片'),
        '#weight' => 1,
        '#attributes' => array('class'=>array('activity-pics')),
        //'#description' => t('These photos used for activity list, It is not a part of activity detail.'),
        'field_logo_thumbnail' => $form['field_logo_thumbnail'],
        'field_activity_logo'  => $form['field_activity_logo'],
        //'div_fix'                  => array('#children'=>'<div class="clearfix"></div>', '#weight'=>100)
    );
    unset($form['field_logo_thumbnail']);
    unset($form['field_activity_logo']);
    */
    //$form['field_source']['#weight'] = 4;
    
    $form['#theme'] = 'activity_form';
    
    $normalFields = array('field_city', 'field_activity_duration', 'field_userlimit', 'field_activity_fee', 'field_activity_type', 'field_assembling_place', 'field_activity_tags');
    
    $form['field_assembling_place']['#weight']= 99;
    $form['field_activity_tags']['#weight'] = 100;
    
    $fieldLang = $form['field_latest_period_time']['#language'];
    
    $form['field_latest_period_time']['#required'] = 1;
    $form['field_activity_endtime']['#required']   = 1;
    $form['field_deadline']['#required']           = 1;
    $form['time'] = array(
        '#type' => 'fieldset',
        //'#title' => t('Activity time'),
        '#weight' => 2,
        //'#description' => t('Time set for Current activity, The activity should be once time activity.'),
        '#attributes' => array('class'=>array('activity-time')),
        'field_latest_period_time' => $form['field_latest_period_time'],
        'field_activity_endtime'   => $form['field_activity_endtime'],
        'field_deadline'           => $form['field_deadline']
    );
    unset($form['field_latest_period_time']);
    unset($form['field_activity_endtime']);
    unset($form['field_deadline']);
    //$form['field_latest_period_time'][$fieldLang][0]['#title'] = t('Start time');
    //$form['field_latest_period_time'][$fieldLang][1]['#title'] = t('End time');
    
    
    $form_additionalInfo = array(
        '#type' => 'fieldset',
        //'#title' => t('活动常规信息'),
        '#weight' => 5,
        '#attributes' => array('class'=> array('normal-info textSpace4'))
    );
    
    foreach ($form as $itemName=>$item) {
        if (in_array($itemName, $normalFields)) {
            $form_additionalInfo[$itemName] = $item;
            unset($form[$itemName]);
        }
    }
    
    $form['additionalInfo'] = $form_additionalInfo;
    
    drupal_add_js('sites/all/libraries/My97DatePicker/WdatePicker.js');
}