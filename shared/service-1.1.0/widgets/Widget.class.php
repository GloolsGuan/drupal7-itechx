<?php
/**
 *
 */

namespace service\widgets;


use service\base\Module;
use service\widgets\models\Widget as WidgetModel;
use service\widgets\models\WidgetInstance as WidgetInstanceModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;


/**
 * Class Widget
 * @package servers\widgets
 * @author yangzy 2017/3/22
 */
class Widget extends Module
{

    /**
     * 添加新插件标题及介绍
     * @param array $data
     */
    function addInstance($data = []){

        $widgetModel = new WidgetModel();
        $widgetModel->title = $data['title'];
        $widgetModel->brief = $data['brief'];
        $widgetModel->save();

        if($widgetModel->id){
            $widgetInstanceModel = new WidgetInstanceModel();
            $widgetInstanceModel->widget_id = $widgetModel->id;
            $widgetInstanceModel->save();
            return $this->buildResponse('success', 200, "添加成功");
        }else{
            return $this->buildResponse('error', 400, "插件库添加失败");
        }
    }
    /**
     * 添加配置项
     * @param array $data
     * @return string
     */
    function addWidget($data = []){
        $model = new WidgetModel();
        $model->title =$data['title'];
        $model->application =$data['application'];
        $model->application_title = $data['application_title'];
        $model->master_title = $data['master_title'];
        $model->master_code = MD5($data['master_title']);
        $model->widget_ns = $data['widget_ns'];
        $model->supported_modes = $data['supported_modes'];
        if(isset($data['logo'])){
            $model->logo = $data['logo'];
        }
        $model->icon = $data['icon'];
        $model->author = $data['author'];
        if($data['author']){
            $model->author_url = "http://blog.dteols.me/widgets/". HASH ('md5', $data['widget_ns']);
        }
        $model->brief = $data['brief'];
        $model->save();
        if($this->id){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 获取所有可用插件
     * @param array $where
     * @return  array 插件列表
     */
    public function listWidgets(array $where = [])
    {
        $ws = WidgetModel::find()->where($where)->asArray()->all();
        return $this->buildResponse('success', 201, $ws);
    }

    /**
     * 根据条件获取实例详情
     * @param array $where
     * @return  array 插件实例列表
     */
    public function getWidgetInstanceById(array $where = [])
    {
        $ws = WidgetInstanceModel::find()->where($where)->asArray()->all();
        if($ws){
            return $ws;
        }else{
            return false;
        }
    }

    /**
     * 添加/更新插件实例
     * @param array $data 实例信息
     * @return mixed 被添加/更新的的实例信息
     */
    public function updateWidgetById(array $data,$instance_id,$widget_id)
    {
        $instance = WidgetInstanceModel::find()->where(['id'=>$instance_id])->one();
        $instance->settings = serialize($data['settings']);
        if($instance->save()){
            $widget = WidgetModel::find()->where(['id'=>$widget_id])->one();
            if(!$widget['predefined_properties']){
                $widget->predefined_properties = serialize($data['predefined_properties']);
                if($widget->save()){
                    return true;
                }else{
                    return false;
                };
            }
            return true;
        }else{
            return false;
        };


    }

    /**
     * 根据条件获取指定插件详情
     * @param array $where
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getWidgetById($where = []){
        $data = WidgetInstanceModel::find()->with("widget")->where($where)->asArray()->one();
        $widget = $data['widget'][0];
        $settings = unserialize($data['settings']);
        $predefined_properties = unserialize($widget['predefined_properties']);
        $data['settings'] = $settings;
        return $data;
    }

    /**
     * 获取所有插件列表，不分页
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getWidgets(){
        $data = WidgetModel::find()->asArray()->all();
        $star = \Yii::$app->get('params')->load('star');
        foreach ($data as $k => &$v){
            $num = WidgetInstanceModel::find()->andWhere(['widget_id' => $v['id']])->count('id');
            $v['instancesNum'] = $num;
            $ss = [];
            $html = '';

            if($num){
                $num = number_format($num / 20,2);
                $s1 = (int)$num;
                $s2 = (int)(substr($num,-2) / 5);
                if($s1){
                    for($i = 0; $i<=$s1; $i++){
                        $ss[$i] = $star["l5"];
                    }
                };
                $v['s2'] = $s2;

                if($s2){
                    if($s2 >= 15){
                        $ss[] = $star["l4"];
                    }else if($s2 >= 10){
                        $ss[] = $star["l3"];
                    }else if($s2 >= 5 || $s2 > 0){
                        $ss[] = $star["l2"];
                    }
                }
            };

            for($i=0;$i<5;$i++){
                if(isset($ss[$i])){
                    $html .= "<img src='" . $ss[$i] . "'>";
                }else{
                    $html .= "<img src='" . $star["l1"] . "'>";
                }
            }
            $v['star'] = $html;
        }
        return $data;
    }

    /**
     * 通过获取$widgetId写入widget_instance实例表
     * @param $widgetId
     * @return bool
     */
    public function insertInstanceByWidgetId($widgetId,$masterExtId){
        if(!$widgetId){
            return false;
        };
        if(!$masterExtId){
            return false;
        };
        $WidgetInstanceModel= new WidgetInstanceModel;
        $WidgetInstanceModel->widget_id = $widgetId;
        $WidgetInstanceModel->master_ext_id = $masterExtId;
        if($WidgetInstanceModel->save()){
            $id = $WidgetInstanceModel->id;
            $WidgetInstanceModelV2 = WidgetInstanceModel::find()->where(['id'=>$id])->one(); //获取name等于test的模型
            $WidgetInstanceModelV2->weight = $id; //修改age属性值
            $WidgetInstanceModelV2->save();   //保存
            return $id;
        }else{
            return false;
        }
    }

    /**
     * 修改排序，根据实例ID的数组
     * @param array $weightList
     * @param array $widgetInstanceIdList
     * @return bool
     */
    public function editWeightListByWidgetInstanceIdList($weightList = array(),$widgetInstanceIdList = array()){
        if(is_array($weightList) && is_array($widgetInstanceIdList)){
            foreach ($widgetInstanceIdList as $k => $v){
                $widgetInstance = WidgetInstanceModel::find()->where(['id'=>$v])->one();
                $widgetInstance->weight = $weightList[$k];
                $widgetInstance->save();
            }
            return true;
        }else{
            return false;
        }
    }

    /**
     * 根据实例ID，删除实例
     * @param $instanceId
     * @return bool
     * @throws \Exception
     */
    public function moveInstanceByInstanceId($instanceId){
        if($instanceId){
            $widgetInstance = WidgetInstanceModel::find()->where(['id'=>$instanceId])->one();
            if($widgetInstance){
                $widgetInstance->delete();
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * 根据计划ID获取对应的组件
     * @param $planId
     * @return array
     */
    public function getWidgetsByForPlan($plan){
        $newArray = [];
        $planId = $plan['plan_id'];
        if(!$plan){
            return $newArray;
        };
        $where = [
            "widget_instance.master_ext_id" => $planId,
        ];
        $order = "widget_instance.weight asc";
        $instances = WidgetInstanceModel::find()->with("widget")
            ->where($where)
            ->groupBy("widget_instance.id")
            ->orderBy($order)
            ->asArray()->all();
        if(!$instances){
            return $this->buildResponse('success', 200, "暂未添加插件");
        }
        foreach ($instances as $k => $v){
            $widget = $v['widget'][0];
            $mode = json_decode($widget['supported_modes'],true);
            $newArray[] = [
                "id" => $v['id'],
                "title" => $widget['title'],
                "widget_name" => $widget['widget_ns'],
                "mode" => $mode['mode_name'],
                "delta" => md5($v['id']),
                "plan"  => $plan,
                "logo"  => \Yii::getAlias('@web')."/widgets/xplan".$widget['icon']
            ];
        }

        return $this->buildResponse('success', 201,$newArray);
    }
}