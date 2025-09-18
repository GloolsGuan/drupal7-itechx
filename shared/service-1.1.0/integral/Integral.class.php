<?php
namespace service\integral;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class Integral extends \service\base\Module{
    
    
    
    public function buildProject($integral_entity_project=[]){
        if (empty($integral_entity_project)) {
            return $this->buildResponse('error', 400, 'There is no integral_entity_project');
        }
        
        $model_integral = new models\Integral();
        $model_integral->setAttributes($integral_entity_project);
        $is_validated = $model_integral->validate();
        
        if (false===$is_validated){
            return $this->buildResponse('error', 401, $model_integral->getErrors());
        }
        
        try{
            $re = $model_integral->save();
            if (true==$re){
                $model_integral->refresh();
            }
        }catch(\Exception $e){
            return $this->buildResponse('failed', 500, $e->getMessage());
        }
        return $this->buildResponse('success', 201, $model_integral->getAttributes());
    }
    
    
    public function logIntegral($log=[]){
        if (empty($log)) {
            return $this->buildResponse('error', 400, 'Integral log entity is required.');
        }
        
        $model_integral_log = new models\IntegralLog();
        $model_integral_log->setAttributes($log);
        $is_validated = $model_integral_log->validate();
        
        if (false===$is_validated){
            return $this->buildResponse('error', 401, $model_integral_log->getErrors());
        }
        
        try{
            $re = $model_integral_log->save();
            if (true==$re){
                $model_integral_log->refresh();
            }
            
        }catch(\Exception $e){
            return $this->buildResponse('failed', 500, [$e->getMessage(), $model_integral_log->getErrors()]);
        }
        
        return $this->buildResponse('success', 201, $model_integral_log->getAttributes());
    }
    
    
    /**
     * Load integral project by id
     * @param type $project_id
     */
    public function loadProject($id){
        ;
    }
    
    
    /**
     * Load integral project by master_code
     * 
     * @param type $master_code
     * @param type $stra
     */
    public function loadProjectByCode($master_code){
        $integral_project_entity = models\Integral::find()->where(['master_code'=>$master_code])->asArray()->one();
        \GtoolsDebug::testLog(__METHOD__, $integral_project_entity);
        if (!empty($integral_project_entity)) {
            return $this->buildResponse('success', 201, $integral_project_entity);
        }
        
        return $this->buildResponse('success', 200, null);
    }
    
    
    public function loadLogsForProject($project_id, $master_ext_id=0){
        
    }
    
    
    /**
     * Load grouped statistics
     * 
     *   Group logs statistics grouped by integral_type.
     * 
     * @param type $project_id
     * @param type $master_ext_id
     */
    public function loadGroupedLogStatistics($project_id, $master_ext_id){
        
    }
}