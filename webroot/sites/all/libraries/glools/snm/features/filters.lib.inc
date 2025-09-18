<?php
/*
 * Created on 2011-10-25
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

Lib_Glools_load::loadFile('glools/snm/features/abstract', 'lib');

class Lib_Glools_Snm_Features_Filters extends Lib_Glools_Snm_Features_Abstract {
	
    protected $featureElements = array();
    protected $filterSessions = null;
    protected $name = '';
    protected $featureName = 'filters';
    protected $defaultNodeFilters = array();
    
    
    public function buildFeatureElement(){
    	$this->buildFilterForm();
    }
    
    protected function buildFilterForm(){
        $this->featureElements = array(
            '#type' => 'fieldset',
            '#title' => t('Show only items where'),
            '#weight'  => 0,
            '#theme' => 'exposed_filters__node',
        );
        
        $this->getDefaultNodeFilters();

        $this->parseSessionFilters();
        
        $this->featureElements['status'] = array(
            '#type' => 'container',
            '#attributes' => array('class' => array('clearfix')),
            '#prefix' => (!empty($this->featureElements['current']) ? '<div class="additional-filters">' . t('and where') . '</div>' : ''),
            'filters' => array(
                '#type' => 'container',
                '#attributes' => array('class' => array('filters')),
            ),
            'actions' => array(
                '#type' => 'actions',
                '#attributes' => array('class' => array('container-inline')),
                'submit' => array(
                    '#type' => 'submit',
                    '#value' => count($this->filterSessions) ? t('Refine') : t('Filter')
                )
            )
        );
        
        foreach ($this->defaultNodeFilters as $key => $filter) {
            $this->featureElements['status']['filters'][$key] = array(
                '#type' => 'select',
                '#options' => $filter['options'],
                '#title' => $filter['title'],
                '#default_value' => '[any]',
            );
        }
        
        if (count($this->filterSessions)) {
            $this->featureElements['status']['actions']['undo'] = array('#type' => 'submit', '#value' => t('Undo'));
            $this->featureElements['status']['actions']['reset'] = array('#type' => 'submit', '#value' => t('Reset'));
        }

        drupal_add_js('misc/form.js');
        
        return $this;
    }
    
    protected function parseSessionFilters(){
        
        $this->filterSessions = isset($_SESSION['node_overview_filter']) ? $_SESSION['node_overview_filter'] : array();
        
        foreach ($this->filterSessions as $filter) {
            list($type, $value) = $filter;
            switch($type){
                case 'term':
                    $value = module_invoke('taxonomy', 'term_load', $value);
                    $value = $value->name;
                    break;
                case 'language':
                    $value = ($value == LANGUAGE_NONE) ? t('Language neutral') : module_invoke('locale', 'language_name', $value);
                    break;
                default:
                    $value = $this->defaultNodeFilters[$type]['options'][$value];
            }
            
            $t_args = array('%property' => $this->featureElements[$type]['title'], '%value' => $value);
            $this->featureElements['current'][] = empty($form['filters']['current']) 
                                                                 ? $form['filters']['current'][] = array('#markup' => t('where %property is %value', $t_args))
                                                                 : array('#markup' => t('and where %property is %value', $t_args));
            
            if (in_array($type, array('type', 'language'))) {
            // Remove the option if it is already being filtered on.
                unset($this->defaultNodeFilters[$type]);
            }
        }
        
        return $this;
    }
    
    /**
     * The default node filter was setted depand on drupal 7.x core node filter settings.
     * */
    private function getDefaultNodeFilters() {
        if(!empty($this->defaultNodeFilters)){
            return $this->defaultNodeFilters;
        }
        // Regular filters
        $this->defaultNodeFilters = array();
        $this->defaultNodeFilters['status'] = array(
            'title' => t('status'),
            'options' => array(
                '[any]' => t('any'),
                'status-1' => t('published'),
                'status-0' => t('not published'),
                'promote-1' => t('promoted'),
                'promote-0' => t('not promoted'),
                'sticky-1' => t('sticky'),
                'sticky-0' => t('not sticky'),
            ),
        );
      
        // Include translation states if we have this module enabled
        if (module_exists('translation')) {
            $this->defaultNodeFilters['status']['options'] += array(
                'translate-0' => t('Up to date translation'),
                'translate-1' => t('Outdated translation'),
            );
        }
      
       //Set type filter field
        $this->defaultNodeFilters['type'] = array('title' => t('type'),'options' => array('[any]' => t('any')));
        $this->defaultNodeFilters['type']['options'] += node_type_get_names();
    
      // Language filter if there is a list of languages
      if ($languages = module_invoke('locale', 'language_list')) {
          $languages = array(LANGUAGE_NONE => t('Language neutral')) + $languages;
          $this->defaultNodeFilters['language'] = array(
              'title' => t('language'),
              'options' => array('[any]' => t('any'))
          );
          $this->defaultNodeFilters['language']['options'] += $languages;
      }

      return $this->defaultNodeFilters;
    }
}