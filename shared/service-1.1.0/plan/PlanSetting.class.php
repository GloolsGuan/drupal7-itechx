<?php

namespace service\plan;
 
use \service\base\Module;
use \service\plan\models\PlanSetting as ModPlanSetting;
use \service\plan\interfaces\PlanSetting as PlanSettingInterfaces;

class PlanSetting extends Module implements PlanSettingInterfaces
{
    /**
     * 保存计划设置
     *
     * @param int $planId 计划id
     * @param array $data 设置信息
     * @return 成功返回true [1]
     * @author drce 20161129
     */
    public function saveSetting($planId, array $data)
    {
        if (empty($planId)) return $this->buildResponse('error', 400, '$planId cannot be empty');
        if (empty($data) || !is_array($data)) return $this->buildResponse('error', 400, '$data must be an array');
        
        $data['plan_id'] = $planId;
        
        if (NULL !== ($planSetting = ModPlanSetting::find()->where(['plan_id' => $planId])->one())){
            $setting = $planSetting->setAttributes($data);
        }else{
            $setting = (new ModPlanSetting())->setAttributes($data);
        }
        
        if (false === $setting->save()) return $this->buildResponse('failed', 400, 'failed to edit PlanSetting resource');
        
        return true;
    }

    /**
     * 获取计划的配置
     *
     * @param int $planId 计划id
     * @return 成功时返回计划的配置 [1]
     * @author drce 20161129
     */
    public function getSetting($planId)
    {
        if (empty($planId)) return $this->buildResponse('error', 400, '$planId cannot be empty');
        
        if (NULL === ($planSetting = ModPlanSetting::find()->where(['plan_id' => $planId])->one())) return $this->buildResponse('failed', 400, 'PlanSetting does not exist');
        
        $status = empty($rows = $planSetting->getAttributes()) ? 200 : 201;
        
        return $this->buildResponse('success', $status, $rows);
    }

}