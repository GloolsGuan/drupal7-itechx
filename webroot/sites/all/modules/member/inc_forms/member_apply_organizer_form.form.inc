<?php
/*
 * Created on Mar 7, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

function member_apply_organizer_form($form, &$form_state){
    global $user;
    $retrieved = db_query('select * from {users_organizer} where uid=:uid', array(':uid'=>$user->uid))->fetchAllAssoc('uid');
    
    if (!empty($retrieved)) {
        $organizerInfo = array_pop($retrieved);
    }
    $form = array(
        '#type'           => 'form',
        '#weight'         => 0,
        '#theme_wrappers' => array('glools_form_wrapper'),
        '#title'          => empty($organizerInfo)? t('Apply for Organizer') : t('Apply for Organizer') . '&nbsp;&nbsp;&nbsp;<span class="description">('. t('Change') .')</span>',
        '#description'    => t('Welcome to be a organizer.'),
        '#attributes'     => array('method'=>'POST', 'class'=>'member_apply_organizer_form'),
        '#validate'       => array('glools_form_validate'),
        '#submit'         => array('glools_form_submit')
    );
    
    $form['otype'] = array(
        '#type'             => 'radios',
        '#title'            => t('Individual or Enterprise'),
        '#weight'           => 0,
        '#required'         => 1,
        '#default_value'    => empty($organizerInfo) ? 'individual' : $organizerInfo->otype,
        '#maxlength'        => 100,
        '#options'           => array(
            'individual' => t('Individual'),
            'enterprise' => t('Enterprise')
        )
        
    );
    
    $form['alipay'] = array(
        '#type' => 'textfield',
        '#title' => t('Alipay account'),
        '#weight' => 1,
        '#required' => 1,
        '#default_value'    => empty($organizerInfo) ? '' : $organizerInfo->alipay,
        '#element_validate' => array('element_validate_email'),
        '#description'      => t('Alipay account should be an email address which you can receive money and pay money by this account.'),
        '#ajax' => array(
            'event' => 'blur',
            'callback' => 'glools_form_element_validate_ajax_callback',
            'effect'   => 'fade'
        )
    );
        
    $form['interesting'] = array(
        '#type'   => 'textarea',
        '#weight' => 2,
        '#title'  => t('What is your intersting in about travel, living and something more?'),
        '#required' => 1,
        '#default_value'    => empty($organizerInfo) ? '' : $organizerInfo->interesting,
        '#rows'     => 5
    );
    
    $form['intention'] = array(
        '#type' => 'textarea',
        '#weight' => 3,
        '#title' => t('Activity intention'),
        '#description' => t('What kinds of activity do you want to build?'),
        '#rows'     => 5,
        '#default_value'    => empty($organizerInfo) ? '' : $organizerInfo->intention,
        '#required' => 1,
    ); 
    
    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Commit'),
        '#weight' => 100,
        
    );
    
    return $form;
}

function member_apply_organizer_form_validate($form, &$form_state){
    
}

function member_apply_organizer_form_submit($form, &$form_state){
    global $user;
    
    $values = $form_state['values'];
    
    $retrieved = db_query('select * from {users_organizer} where uid=:uid', array(':uid'=>$user->uid))->fetchAllAssoc('uid');
    if (empty($retrieved)) {
        $sql = 'insert into {users_organizer} (`uid`,`otype`,`verify`, `status`, `alipay`, `interesting`, `created`, `intention`) values(:uid, :otype, :verify, :status, :alipay, :interesting, :created, :intention);';
    }else{
        $sql = 'update {users_organizer} set `uid`=:uid, `otype`=:otype, `verify`=:verify, `status`=:status, `alipay`=:alipay, `created`=:created, `intention`=:intention where uid=:uid';
    }
    
    $queried = db_query($sql, array(':uid'=>$user->uid, ':otype'=>$values['otype'], ':verify'=>'waiting', ':status'=>'inactive', ':alipay'=>$values['alipay'], ':interesting'=>$values['interesting'], 'created'=>date('Y-m-d H:i:s', time()), 'intention'=>$values['intention']));
    if (1==$queried->rowCount()) {
        drupal_set_message(t('You have commit your application successfully.'));
    }else{
        drupal_set_message(t('Failed to commit your application.'), 'error');
    }
    
    return true;
}