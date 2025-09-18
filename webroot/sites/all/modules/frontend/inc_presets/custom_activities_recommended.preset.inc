<?php

/**
 * 
 * 
 */
function _frontend_preset_custom_activities_recommended($view){
	$cityTerm = cache_get('current_city_term');
    
    if (empty($cityTerm->data)) {
        return null;
    }
    $cityTermId = $cityTerm->data->tid;
    
    $modelActivity = Lib_Glools_Load::load('activity', 'model');
    $nodes = $modelActivity->loadPromotedActivities(null, $cityTermId, 3);
    
    return node_view_multiple($nodes, 'teaser'); 
}

function _frontend_preset_activities_recommended_pager($view){
    $cityTerm = cache_get('current_city_term');
    
    if (empty($cityTerm->data)) {
        return null;
    }
    $cityTermId = $cityTerm->data->tid;
    Glools_Inc_Tools::loadInc('pager', 'lib', 'frontend');
    $query = db_select('node', 'n')->extend('GloolsPagerDefault');
    $query->join('field_data_field_city', 'c', "c.entity_id =n.nid");
    $query->fields('n', array('nid'));
    $query->condition('n.type', 'activity');
    $query->condition('n.status', 1);
    $query->condition('n.promote', 1)
          //->condition('n.sticky', 1)
          ->condition('c.field_city_tid', $cityTermId);
    $query->limit(3);
    $query->addMetaData('page-owner', 'activities/recommended/pager');
    
    $fetched = $query->execute()->fetchAllAssoc('nid');
    
    return array_keys($fetched);
}