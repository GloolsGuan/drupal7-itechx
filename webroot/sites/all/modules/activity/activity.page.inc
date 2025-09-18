<?php

function activity_lock($aid){
	global $user;
    
    $node = node_load($aid);
    
    if ($node->uid==$user->uid) {
    	$rQ = db_query('update {node} set status=0 where nid=:nid', array(':nid'=>$node->nid));
        $rQ = db_query('update {node_revision set status=0 where nid=:nid and vid=:vid}', array(':nid'=>$node->nid, ':vid'=>$node->vid));
    }
    
    $re = array(
        'status' => 1,
        'action' => 'Lock',
        'data' => array(
            'text' => 'Unlock',
            'status' => 0,
            'node'   => $aid,
            'url' => url('activity/' . $aid . '/unlock')
        )
    );
    
    print json_encode($re);
}

function activity_unlock($aid){
	global $user;
    
    $node = node_load($aid);
    
    if ($node->uid==$user->uid) {
        $rQ = db_query('update {node} set status=1 where nid=:nid', array(':nid'=>$node->nid));
        $rQ = db_query('update {node_revision set status=1 where nid=:nid and vid=:vid}', array(':nid'=>$node->nid, ':vid'=>$node->vid));
    }
    
    $re = array(
        'status' => 1,
        'action' => 'Lock',
        'data' => array(
            'text' => 'Lock',
            'status' => 1,
            'node'   => $aid,
            'url' => url('activity/' . $aid . '/lock')
        )
    );
    
    print json_encode($re);
}

function activity_favorite($aid){
	global $user;
    $rQ = db_query('delete from {flag_content}  where uid=:uid and content_id=:cid', array(':uid'=>$user->uid, 'cid'=>$aid));
    if ($rQ->rowCount()>0) {
        db_query('update {flag_counts} set count=count-1 where content_id=:cid', array(':cid'=>$aid));
    	$re = array(
            'status' => 1,
            'action' => 'Favorite',
            'data' => array(
                'text' => 'canceled',
                'status' => 1,
                'node'   => $aid
            )
        );
    }else{
    	$re = array(
            'status' => 1,
            'action' => 'favorite',
            'data' => array(
                'text' => 'favorite',
                'status' => 0,
                'node'   => $aid
            )
        );
    }
    print json_encode($re);
}



function activity_test(){
    return 'Hello,world!';
}
