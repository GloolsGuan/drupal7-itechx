<?php
/*
 * Created on Jun 3, 2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function member_organizer_flist($status=''){
	global $user;
    
    $select = db_select('users', 'u')->extend('GloolsPagerDefault');
    $select->fields('u', array('uid', 'name', 'mail', 'picture'));
    $select->fields('o', array('otype', 'verify', 'status', 'level'));
    $select->rightJoin('users_organizer', 'o', 'o.uid=u.uid');
    $select->condition('u.status', 1);
    
    $rows = $select->execute()->fetchAllAssoc('uid');
    if (empty($rows)) {
        return 'There is no organizer registered.';
    }
    
    $libSnm = Lib_Glools_Load::load('glools/snm', 'lib');
    
    
    $libSnm->setFormProperties(
        array('#submit'=>array('member_organizer_flist_submit'))
    );
    
    $libSnm->setFeatures(
        array(
            'filters'    => array('data' => '', 'options' => array('name'=>"hello,world")), 
            'operations' => array('options'=>array('operationHooks'=>'materialRepositry_operations')),
            'nodes'      => array('data'=>$rows, 'options'=>array()),
            'pager'      => array()
        )
    );
    
    return $libSnm->getForm();
}

function member_organizer_flist_submit(&$form, &$form_state){
    
}