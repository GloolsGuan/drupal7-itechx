<?php


function activity_participating_agreement($form, &$form_state){
	$form = array();
    
    preg_match('/^activity\/([0-9]+)\/book$/', request_path(), $matched);
    
    $form = array(
        '#type'    => 'form',
        '#weight'  => 0,
        '#validate' => array('activity_form_validate'),
        '#theme_wrappers' => array('glools_form_wrapper'),
        '#submit'   => array('activity_form_validate'),
        //'#title' => t('Activity participating agreement'),
        //'#description' => t('Please be sure your contact information is valid.')
        '#attributes' => array('method'=>'POST')
    );
    
    $form['agreement'] = array(
        '#theme' => 'div',
        '#vars'  => array(
            'content' => Glools_Inc_Tools::renderTpl(array(), 'activity-agreement', 'activity'),
            'attributes' => array('class'=>'activity-agreement')
        )
    );
    
    $form['activity_id'] =array(
        '#type' => 'hidden',
        '#value' => $matched[1]
    );
    
    $form['agree'] = array(
        '#type' => 'button',
        '#value' => t('Agree activity participating agreement'),
        '#ajax' => array(
            'event'    => 'click',
            'callback' => 'activity_ajax_render',
            'method'   => 'replace'
        )
    );
    
    return $form;
}

function activity_participating_agreement_validate(&$form, &$form_state){
    global $user;
	
}

function activity_participating_agreement_submit(&$form, &$form_state){
    global $user;
    if (empty($form_state['values']['agree'])) {
    	return;
    }
    
    
    $retrieve = db_query('select * from {activity_participant} where user_id=:uid and instance_id=:iid', array(':uid'=>$user->uid, 'iid'=>$form_state['values']['activity_id']))->fetchAllAssoc('uid');
    
    if (empty($retrieve)) {
        $fields = array(
            ':iid' => $form_state['values']['activity_id'],
            ':uid' => $user->uid,
            ':status' => 'agreed agreement'
        );
    	$queried = db_query('insert into {activity_participant}(`instance_id`, `user_id`, `status`) values(:iid, :uid, :status)', $fields);
        
        $user->activity = array('agreed agreement'=>$form_state['values']['activity_id']);
    }
    
}

