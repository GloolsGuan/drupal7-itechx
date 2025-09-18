<?php
/*
 * Created on 2011-11-7
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
function home_activity(){
    global $user;
    
	$breadcrumbs = array(
        theme_link(array('path'=>'<front>', 'text'=>'Lasooo', 'options'=>array())),
        theme_link(array('path'=>'home', 'text'=>'My home', 'options'=>array())),
        theme_link(array('path'=>'home/activity', 'text'=>'messages', 'options'=>array()))
    );
    
    drupal_set_breadcrumb($breadcrumbs);
    
    $pars = explode('/', request_path());
    if (count($pars)>2 && !empty($pars[2])) {
    	$tab = $pars[2];
    }else{
    	$tab = 'created';
    }
    
    $activities = home_activity_loadMyActivities($tab);
    $tabs       = home_activity_tabs();
    $state       = home_activity_state();
    
    drupal_add_css(path_to_theme() . '/css/pages/home-activity.page.css');
    
    drupal_add_js(path_to_theme() . '/js/gajax.js');
    
    return $activities;
    //return theme('activity_home', array('tabs'=>$tabs, 'nodes'=>$activities, 'state'=>$state));
}


function home_activity_state(){
    return 'hello,world';
}


function home_activity_loadMyActivities($tab){
    global $user;
	if (1>$user->uid || 1>$user->status) {
        drupal_set_message(t('You have no permission to visit the page, Please login first.'), 'error');
    }
    
    $sQuery = db_select('node', 'n')->extend('GloolsPagerDefault');
    $sQuery->fields('n', array('nid'));
    $sQuery->condition('n.type', 'activity');
    switch($tab) {
    	case 'created':
            $sQuery->condition('uid', $user->uid);
            break;
        case 'participent':
            $sQuery->rightJoin('activity_participant', 'p', 'p.instance_id=n.nid');
            $sQuery->condition('p.user_id', $user->uid);
            break;
        case 'favorite':
            $rQ = db_query('select * from {flag_content} as c right join {flags} as f on (f.name=\'like\') where c.uid=:uid', array(':uid'=>$user->uid));
            if ($rQ->rowCount()>0) {
            	$nids = array_keys($rQ->fetchAllAssoc('content_id'));
            }else{
            	return t('There is no favorite activity retrieved.');
            }
            
            $sQuery->condition('n.nid', $nids, 'in');
            break;
        default:
            $sQuery->condition('uid', $user->uid);
    }
    
    //$sQuery->condition('status', 1);
    $sQuery->orderBy('n.created', 'desc');
    $sQuery->limit(5);
    $sQuery->addMetaData('page-owner', 'home/activities');
    $eQ = $sQuery->execute();
    
    
    if ($eQ->rowCount()>0) {
        $nIds = $eQ->fetchAllAssoc('nid');
        $nodes = node_load_multiple(array_keys($nIds));
        $nodeBuild = node_view_multiple($nodes, 'hlist');
        $build = array(
            'nodes' =>$nodeBuild,
            'pager' => array(
                '#theme'   => 'glools_pager',
                '#weight'  => 5,
                '#quantity' => 3,
                '#element' => 'home/activities'
            )
        );
        
        return $build;
    }
    
    return array('#children'=>t('There is no activity retrieved.'));
}

function home_activity_tabs(&$vars, $hook){
    
    if (preg_match('/^home\/activities\/([a-z]+)/', request_path(), $matched)) {
        $tab = $matched[1];
    }else{
        $tab = 'created';
    }
    
    $mtabs = array(
        '#type' => 'ul',
        'cAttributes' => array(),
        'items' => array(
            //array('data'=>l('All', 'home/activities'), 'attributes'=>array('id'=>'tabs-all', 'class'=>array('tabs-all'))),
            array('data'=>l('Created', 'home/activities/created'), 'attributes'=>array('id'=>'mbox-tabs-created', 'class'=>array('tabs-created'))),
            array('data'=>l('Participent', 'home/activities/participent'), 'attributes'=>array('id'=>'mbox-tabs-participent', 'class'=>array('tabs-participent'))),
            array('data'=>l('Favorite', 'home/activities/favorite'), 'attributes'=>array('id'=>'mbox-tabs-favorite', 'class'=>array('tabs-favorite'))),
            //array('data'=>l('Marked star', 'home/activities/marked'), 'attributes'=>array('id'=>'mbox-tabs-marked', 'class'=>array('tabs-marked'))),
            //array('data'=>l('Expired', 'home/activities/expired'), 'attributes'=>array('id'=>'mbox-tabs-expired', 'class'=>array('tabs-expired'))),
            //array('data'=>l('Failed', 'home/activities/failed'), 'attributes'=>array('id'=>'mbox-tabs-failed', 'class'=>array('tabs-failed'))),
        )
    );
    
    $tabClass = 'tabs-' . $tab ;
    foreach($mtabs['items'] as $k=>$v) {
    	if (in_array($tabClass, $v['attributes']['class'])) {
    		$v['attributes']['class'][] = 'active';
            $mtabs['items'][$k] = $v;
            break;
    	}
    }
    
    $buildTabs = theme('glools_item_list', $mtabs);
    
    return $buildTabs;
}