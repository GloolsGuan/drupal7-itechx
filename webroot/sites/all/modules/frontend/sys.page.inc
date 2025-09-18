<?php
/*
 * Created on Sep 8, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function frontend_sys_denied($originalPath=''){
    
    //TODO: design a single page for render, the page should be in [theme]/templates/pages/denied.tpl.php
    $mes = 'You are not allowed to viste the path "!path" as ' .
            'a unauthenticated visiter. You need to !login a ' .
                    'account, Welcome you to be a member of Lasooo Travel.';
    
    return t($mes,  array('!path'=>url(base64_decode($originalPath)), '!login'=>l(t('Register'), 'user/register')));
}

