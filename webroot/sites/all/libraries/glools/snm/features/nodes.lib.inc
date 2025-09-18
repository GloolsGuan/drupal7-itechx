<?php
/*
 * Created on 2011-10-26
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
Lib_Glools_load::loadFile('glools/snm/features/abstract', 'lib');

class Lib_Glools_Snm_features_Nodes extends Lib_Glools_Snm_features_Abstract {
    
    protected $featureElements = array();
    protected $featureName = 'admin';
    protected $defaultNodeOperations = array();
    protected $nodes = array();
    protected $header = array();
    protected $multilanguage = null;
    protected $renderHook = '';
 
    
    
    public function buildfeatureElement(){
    	
        if(!is_array($this->data) || empty($this->data)){
            $this->setMessage("There is no valid data provided to ".__METHOD__, 'notice');
        }
        
        $this->multilanguage = (module_exists('translation') || db_query_range("SELECT 1 FROM {node} WHERE language <> :language", 0, 1, array(':language' => LANGUAGE_NONE))->fetchField());
        
        $this->buildHeader();
        
        if(!is_array($this->data)){
        	return false;
        }
        
        foreach($this->data as $node){
            $this->nodes[$node->nid] = $this->buildNode($node);
        }
        
        $this->buildRenderElement();
        return $this;
    }
    
    protected function buildHeader(){
    	$this->header = array(
            'title'   => array('data' => t('Title'), 'field' => 'n.title'),
            'type'    => array('data' => t('Type'), 'field' => 'n.type'),
            'author'  => t('Author'),
            'status'  => array('data' => t('Status'), 'field' => 'n.status'),
            'changed' => array('data' => t('Updated'), 'field' => 'n.changed', 'sort' => 'desc')
        );
        
        if ($this->multilanguage) {
            $this->header['language'] = array('data' => t('Language'), 'field' => 'n.language');
        }
        
        $this->header['operations'] = array('data' => t('Operations'));
        return $this;
    }
    
    protected function buildNode($node){
    	$languages = language_list();
        $l_options = ($node->language != LANGUAGE_NONE && isset($languages[$node->language])) 
                         ? array('language' => $languages[$node->language]) : array();
                         
        $re = array(
                'title' => array(
                    'data' => array(
                        '#type' => 'link',
                        '#title' => $node->title,
                        '#href' => 'node/' . $node->nid,
                        '#options' => $l_options,
                        '#suffix' => ' ' . theme('mark', array('type' => node_mark($node->nid, $node->changed))),
                    ),
                ),
                'type' => check_plain(node_type_get_name($node)),
                'author' => theme('username', array('account' => $node)),
                'status' => $node->status ? t('published') : t('not published'),
                'changed' => format_date($node->changed, 'short')
        );
        
        if ($this->multilanguage) {
            if ($node->language == LANGUAGE_NONE || isset($languages[$node->language])) {
                $re['language'] = $node->language == LANGUAGE_NONE ? t('Language neutral') : t($languages[$node->language]->name);
            }
            else {
                $re['language'] = t('Undefined language (@langcode)', array('@langcode' => $node->language));
            }
        }
        $re['operations'] = $this->buildNodeOperations($node);
        
        return $re;
    }
    
    protected function buildNodeOperations($node){
    	static $destination ;
        $destination = drupal_get_destination();
        
        $operations = $re = array();
        
        if (node_access('update', $node)) {
            $operations['edit'] = array(
                'title' => t('edit'),
                'href' => 'node/' . $node->nid . '/edit',
                'query' => $destination
            );
        }
        if (node_access('delete', $node)) {
            $operations['delete'] = array(
                'title' => t('delete'),
                'href' => 'node/' . $node->nid . '/delete',
                'query' => $destination
            );
        }
        
        if (count($operations) > 1) {
          // Render an unordered list of operations links.
          $re = array(
              'data' => array(
                  '#theme' => 'links__node_operations',
                  '#links' => $operations,
                  '#attributes' => array('class' => array('links', 'inline')),
              )
          );
        }
        elseif (!empty($operations)) {
          // Render the first and only operation as a link.
          $link = reset($operations);
          $re = array(
              'data' => array(
                  '#type' => 'link',
                  '#title' => $link['title'],
                  '#href' => $link['href'],
                  '#options' => array('query' => $link['query'])
              )
          );
        }
        
        return $re;
    }
    
    protected function buildRenderElement(){
    	if(empty($this->renderHook)){
    		$this->renderHook = 'tableselect';
    	}
        
        switch($this->renderHook){
        	case 'tableselect':
                $this->featureElements['nodes'] = array(
                    '#type' => 'tableselect',
                    '#header' => $this->header,
                    '#options' => $this->nodes,
                    '#weight'  => 10,
                    '#empty' => t('No content available.')
                );
                break;
            case 'table':
                $this->featureElements['nodes'] = array(
                    '#theme' => 'table',
                    '#header' => $this->header,
                    '#weight'  => 10,
                    '#rows' => $this->nodes,
                    '#empty' => t('No content available.')
                );
                break;
            default:
                $this->featureElements['nodes'] = array(
                    '#theme' => $this->renderHook,
                    '#header' => $this->header,
                    '#weight'  => 10,
                    '#rows' => $this->nodes,
                    '#empty' => t('No content available.')
                );
        }
    }
}