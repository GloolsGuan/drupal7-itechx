<?php

namespace service\plan;
 
use \service\base\Module;
use \service\plan\models\Plan as ModPlan;
use \service\plan\models\RsPlanCatalog;
use \service\plan\interfaces\PlanSearch as PlanSearchInterfaces;

class PlanSearch extends Module implements PlanSearchInterfaces
{
    /**
     * 搜索计划列表（要支持价格区间条件和所属课程目录(catalog)条件）
     *
     * @param array $map 搜索条件 格式参照yii2 where参数格式
     * @param int $offset 偏移
     * @param int $limit 数据量限制
     * @param array $order 排序 格式参照 yii2 order参数格式
     * @return array [1]
     */
    public function listPlans(array $map = [], $offset = 0, $limit = 20, array $order = [])
    {
        
        $query = ModPlan::find()->from(['p' => ModPlan::tableName()])
        ->select(['p.*'])
        ->where($map);
        
        if(isset($map['catalog_id'])){
            $query->join('LEFT JOIN', RsPlanCatalog::tableName() . ' AS rs', 'p.plan_id = rs.plan_id AND rs.catalog_id = "'.$map['catalog_id'].'"');
        }
        
        
        $query->offset($offset)->limit($limit);
        
        if(!empty($order)){
            foreach ($order as $k => $v){
                $query->addOrderBy($v);
            }
        }else{
            $query->addOrderBy('p.plan_ct DESC')->addOrderBy('p.plan_id');
        }
        
        $plans = $query->asArray()->all();
        
        $status = empty($plans) ? 200 : 201;
        
        return $this->buildResponse('success', $status, ['data' => $plans, 'total' => $query->count()]);
    }

}