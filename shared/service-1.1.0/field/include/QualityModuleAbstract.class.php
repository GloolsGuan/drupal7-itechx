<?php
// +----------------------------------------------------------------------
// | Description [ module for quality ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015012 cr.cn.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: ouchao <ouchao1234@163.com>
// +----------------------------------------------------------------------

namespace app\widgets\field\inc;

use app\widgets\field\inc\LoadQualityConf;

//require_once(dirname(__FILE__) .'/../../common/LoadMessage.php');
//require_once(dirname(__FILE__) .'/LoadQualityConf.php');

class QualityModuleAbstract{
	
	//根据某个具体的组件配置构建数据表
	public function buildSingleQualityTable($data=array()){
		$flag = false;
		$tool =  new LoadQualityConf();
		$flag =$tool->createQualityTableSql($data['name'],$data['fields']);
		$flag = $tool->createQualityTableSql($data['name'],$data['required_fields'],'required');
		$flag = $tool->createQualityTableSql($data['name'],$data['upload_fields'],'file');
		return $flag;
	}

    //输出目前可用的widget
	public function loadWidgets($complete=false,$type=false){
		echo "111";
		return false;
		$files = array();
		$dir = dirname(__FILE__).'/..';
		$tool =  new LoadQualityConf();
		if ( $handle = opendir($dir) ) {
			while ( ($file = readdir($handle)) !== false )
			{
				if ( $file != ".." && $file != "." && $file != 'include')
				{
					if ( is_dir($dir .'/'. $file) ){
						$val = $tool->getQualityByName($file);
						if($complete == false){
							$files[$val['category']][] = array('name'=>$val['name'],'label'=>$val['label'],'sort'=>$val['sort']);
						}else{
							if($type==false)
								$files[] =$val;
							else
								$files[] = array('name'=>$val['name'],'label'=>$val['label'],$type=>$val[$type],'sort'=>$val['sort']);
						}
					}
				}
			}
			closedir($handle);
		}
		//按照sort重排序
		if(!empty($files)){
			foreach($files as $k=>$v){
				if(count($v)>1){
					foreach ($v as $key => $val) {
						$sort[$key] = $val['sort'];
					}
					array_multisort($sort,SORT_DESC,$v);
					$files[$k] = $v;
				}
				
			}
		}

		return $files;
	}
	
	//输出单个组件widget
	public function loadSingleQuailtyWidget($quality_name='',$widget_type,$object_id=null){
		$widget = array();
		$tool =  new LoadQualityConf();
		$quality = $tool->getQualityByName($quality_name);
		if($quality[$widget_type]){
			foreach ($quality[$widget_type] as $k=>$v){
				$_tmp = array(
						'name'=>$v['field_name'],
						'label'=>$v['label'],
						'type'=>$v['widget']['type'],
						'suffix'=>$v['widget']['suffix'],
						'value'=>isset($v['default_value']) ? $v['default_value'] : null,
						'unique'=>isset($v['widget']['unique']) ? $v['widget']['unique'] : false,
						'value_callback'=>isset($v['value_callback']) ? $v['value_callback'] : null,
				);

				if(isset($v['related_with'])) $_tmp['related_with'] = $v['related_with'];//产品要求关联字段
				
				//多选选项值输出
				if(!empty($v['value_callback'])){
					if($v['value_callback'] == 'zone') 
						$_tmp['opts'] = Yii::app()->getModule("Zone")->loadZoneData();
					else
						$_tmp['opts'] = Yii::app()->getModule("Constant")->run($v['value_callback']);
				}else{
						$_tmp['opts'] = array();
				}
				if(!empty($object_id)) $_tmp['value'] = $this->loadFieldDefaultVal($quality_name,$widget_type,$v['field_name'],$object_id);

				$widget[$k] = $_tmp;
			}
		}
		return $widget;
	}

	//输出某个字段的默认值
	public function loadFieldDefaultVal($wname,$widget_type,$field_name,$object_id){
		$table_name = 'quality_'.$wname;
		if($widget_type=='required_fields'){
			$table_name .= '_required';
			$where = " where product_id=".$object_id;
		} else{
			$where = " where user_id=".$object_id;
		}

		$sql = "select ".$field_name." from ".$table_name." ".$where;

		$data = null;

		$row = Yii::app()->db->createCommand($sql)->queryRow();

		$data = !empty($row[$field_name]) ? $row[$field_name] : null;
		return $data;
	}

	//组件数据录入入口   
    public function process($form_params, $form_state){
        $bool = $this->entryWidgetData($form_state['widget'],$form_state);
        if($bool !== false){
			echo json_encode(LoadMessage::message('success','101',$form_state['widget']));exit();
		}
    }
	
	//单个组件数据录入处理
	public function entryWidgetData($widget_name='',$params=array()){
		$tool =  new LoadQualityConf();
		$tool->qualityDataEntry($widget_name,$params);
		return true;
	}
	
	//查询（查询产品或者查询用户）
	public function searchByWidget($widget_name='',$widget_data=array(),$target){
	    $tool =  new LoadQualityConf();
		$data = $tool->widgetSearch($widget_name,$widget_data,$target);
		$_tmp = '';
		if(!empty($data)){
			foreach($data as $k=>$v){
				$_tmp .= empty($_tmp) ? $v[$target] : ','.$v[$target];
			}
		}
		return $_tmp;
	}

	//根据查询对象输出单个组件信息
	public function loadWidgetDataByObject($object_id,$widget_name,$widget_type,$complete=false){
		$tool =  new LoadQualityConf();
		$widget = $this->loadSingleQuailtyWidget($widget_name,$widget_type);

		$table_name = ($widget_type=="required_fields") ? "quality_".$widget_name."_required" : "quality_".$widget_name;

		$sql = "select * from ".$table_name." where ";
		$sql .= ($widget_type=="required_fields") ? " product_id=".$object_id : " user_id=".$object_id;

		if($complete==false) $sql .= " and status <> 1";

		$data = array();

		$row =Yii::app()->db->createCommand($sql)->queryRow();

		if(!empty($row)){
			$row = Yii::app()->getModule("Constant")->list_run($row);
			foreach ($widget as $key => $val) {
				if(isset($row[$val['name']])){
					$item = array(
						'name'=>$val['name'],
						'label'=>$val['label'],
						'unique'=>$val['unique'],
						'type'=>$val['type'],
						'value_callback'=>$val['value_callback'],
						'val'=>isset($row[$val['name']."_cn"]) ? $row[$val['name']."_cn"] : $row[$val['name']],
						'real_val'=>$row[$val['name']]
					);

					if(isset($val['related_with'])) $item['related_with'] = $val['related_with'];

					//多选处理
					if($item['type']=='checkbox' || $item['type'] == 'censuses') {
						if(!empty($row[$val['name']])){
							$_tmp = '';
							foreach (explode(",",$row[$val['name']]) as $k => $v) {
								$_tmp[] = Yii::app()->getModule("Constant")->run($item['value_callback'],$v);
							}
							$_tmp = implode("/",$_tmp);
						}
						$item['val'] = $_tmp;
					}
					//添加后缀
					$item['val'] .= (!empty($val['suffix']) && !empty($row[$val['name']])) ? $val['suffix'] : '';
					//区域单独处理
					if($val['type']=='area') {
						$item['val'] = Yii::app()->getModule("Zone")->getZoneName($row[$val['name']]);
						$item['val'] = empty($item['val']) ? '' : $item['val'];
					}elseif($val['type']=='district'){
						if(!empty($item['val'])){
							$_zone = array();
							foreach(explode(",",$item['val']) as $k=>$v){
								$zone_name = Yii::app()->getModule("Zone")->getZoneName($v);
								if(!empty($zone_name)) $_zone[] = $zone_name;
							}
							$item['val'] = implode(",", $_zone);
						}
					}

					//无值情况
					if(empty($item['val'])) $item['val'] = '无';
					
					$data[] = $item;
				}
			}
		} 
		//print_r($data);die;
		return $data;
	}

	//弃用某个组件
	public function cancelWidget($object_id,$widget_name,$table_name_suffix=''){
		$bool = true;
		$table_name = 'quality_'.$widget_name.$table_name_suffix;
		if(empty($table_name_suffix)){
			$bool = Yii::app()->db->createCommand("update ".$table_name." set status=1 where user_id=".$object_id)->execute();
		}else{
			$bool = Yii::app()->db->createCommand("update ".$table_name." set status=1 where product_id=".$object_id)->execute();
		}
		return $bool;
	}
	
}