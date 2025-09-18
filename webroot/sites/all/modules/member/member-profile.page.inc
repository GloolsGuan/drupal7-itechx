<?php
/*
 * Created on Feb 29, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function member_profile($uid=null) {
    if (null===$uid) {
    	$uid = 'hello,world';
    }
	return 'Member profile for '. $uid;
}