<?php
/*
 * Created on 13 Feb, 2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

Lib_Glools_Load::loadFile('glools/snm/form', 'lib');

class Lib_Glools_Comment extends Lib_Glools_Base{
	
    protected $node = null;
    
    public function setNode($node){
    	if(!empty($node) && is_set($node->nid)){
    		$this->node = $node;
    	}
    }
    
    public function commentNumber($node){
    	
    }
    public function postComment(){
    	return 'hello,world';
    }
    
    public function loadComments($node){
    	if ($node->comment_count && user_access('access comments')) {
            $mode = variable_get('comment_default_mode_' . $node->type, COMMENT_MODE_THREADED);
            $comments_per_page = variable_get('comment_default_per_page_' . $node->type, 50);
            if ($cids = $this->loadCommentThread($node, $mode, 10)) {
                $comments = comment_load_multiple($cids);
                comment_prepare_thread($comments);
                $build = comment_view_multiple($comments, $node);
                $build['pager']['#theme'] = 'pager';
                $additions['comments'] = $build;
                return $additions;
            }
        }
        
        return null;
    }
    
    public function loadCommentsByUser(){
    	
    }
    
    public function getCommentForm($node){
    	if (user_access('post comments') && $node->comment == COMMENT_NODE_OPEN && (variable_get('comment_form_location_' . $node->type, COMMENT_FORM_BELOW) == COMMENT_FORM_BELOW)) {
            $build = drupal_get_form("comment_node_{$node->type}_form", (object) array('nid' => $node->nid));
            
            $additions['comment_form'] = $build;
            return $additions;
        }
        return null;
    }
    
    /**
     * Note: The methods come from "comment.module:comment_get_thread();" just alter ASC TO DESC.
     * */
    protected function loadCommentThread($node, $mode, $comments_per_page) {
        $query = db_select('comment', 'c')->extend('PagerDefault');
        $query->addField('c', 'cid');
        $query->condition('c.nid', $node->nid)
              ->addTag('node_access')
              ->addTag('comment_filter')
              ->addMetaData('node', $node)
              ->limit($comments_per_page);
    
        $count_query = db_select('comment', 'c');
        $count_query->addExpression('COUNT(*)');
        $count_query->condition('c.nid', $node->nid)
                    ->addTag('node_access')
                    ->addTag('comment_filter')
                    ->addMetaData('node', $node);
    
        if (!user_access('administer comments')) {
            $query->condition('c.status', COMMENT_PUBLISHED);
            $count_query->condition('c.status', COMMENT_PUBLISHED);
        }
        if ($mode === COMMENT_MODE_FLAT) {
            $query->orderBy('c.cid', 'DESC');
        }
        else {
            // See comment above. Analysis reveals that this doesn't cost too
            // much. It scales much much better than having the whole comment
            // structure.
            $query->addExpression('SUBSTRING(c.thread, 1, (LENGTH(c.thread) - 1))', 'torder');
            $query->orderBy('torder', 'DESC');
        }
    
        $query->setCountQuery($count_query);
        $cids = $query->execute()->fetchCol();
    
        return $cids;
    }
}