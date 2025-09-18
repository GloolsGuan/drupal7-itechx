<?php
/*
 * Created on 2011-10-26
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
Lib_Glools_load::loadFile('glools/snm/features/abstract', 'lib');

class Lib_Glools_Snm_features_Operations extends Lib_Glools_Snm_features_Abstract {
    
    protected $featureElements = array();
    protected $featureName = 'options';
    protected $defaultNodeOperations = array();
    
    // Used for setting special hook name for building operation list. 
    protected $operationHooks = '';
    
    public function buildfeatureElement(){
    	$admin_access = user_access('administer nodes');
        
        $this->featureElements = array(
            '#type' => 'fieldset',
            '#title' => t('Update options'),
            '#weight' => 1,
            '#attributes' => array('class' => array('container-inline')),
            '#access' => $admin_access,
        );
        $options = array();
        
        if( empty($this->operationHooks) ){
        	$options['nooperation'] = "There is no valid operation.";
            $this->setMessage('You can set operationHooks when you set '.__CLASS__.' to add some operations, detail information please read the method documents.', 'notice');
        }else{
        	foreach (module_invoke_all($this->operationHooks) as $operation => $array) {
                $options[$operation] = $array['label'];
            }
        }
        
        
  
        $this->featureElements['operation'] = array(
            '#type' => 'select',
            '#title' => t('Operation'),
            '#title_display' => 'invisible',
            '#weight' => 2,
            '#options' => $options,
            '#default_value' => 'approve',
        );
        $this->featureElements['submit'] = array(
            '#type' => 'submit',
            '#value' => t('Update'),
            '#validate' => array(),
            '#submit' => array(),
        );
    }
}