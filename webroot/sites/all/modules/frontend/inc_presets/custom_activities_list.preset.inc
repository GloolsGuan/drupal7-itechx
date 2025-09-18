<?php

/**
 * 
 * 
 */
function _frontend_preset_custom_activities_list($view){
	$cityTerm = cache_get('current_city_term');
    
    if (empty($cityTerm->data)) {
    	return null;
    }
    $cityTermId = $cityTerm->data->tid;
    
    $modelActivity = Lib_Glools_Load::load('activity', 'model');
    $pagerNodes = $modelActivity->loadLatestWithPager(null, $cityTermId, 8);
    
    $build = node_view_multiple($pagerNodes['nodes'], 'list');
    
    $build['pager'] = array(
      '#theme' => 'glools_pager',
      '#weight' => 5,
      '#element' => $pagerNodes['pagerKey']
    );
    
    return $build;
}

/*
function _frontend_preset_activities_list_pager($view){
	$cityTerm = cache_get('current_city_term');
    
    if (empty($cityTerm->data)) {
        return null;
    }
    $cityTermId = $cityTerm->data->tid;
    Glools_Inc_Tools::loadInc('pager', 'lib', 'frontend');
    $query = db_select('node', 'n')->extend('GloolsPagerDefault');
    $query->join('field_data_field_city', 'c', "c.entity_id =n.nid");
    $query->fields('n', array('nid'));
    $query->condition('n.type', 'activity')
          ->condition('n.status', 1)
          ->condition('c.field_city_tid', $cityTermId);
    $query->limit(8);
    $query->addMetaData('page-owner', 'activities/list/pager');
    
    $fetched = $query->execute()->fetchAllAssoc('nid');
    
    return array_keys($fetched);
}
*/