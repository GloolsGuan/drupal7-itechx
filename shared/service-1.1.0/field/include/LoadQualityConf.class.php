<?php
/**
 *  资质配置类
 * 
 * 
 * 
 * 
 */
namespace app\widgets\field\inc;

class LoadQualityConf{
	private static $filed_conf;
	private  static $filed_type_conf;
	
	//获取字段配置文件
	public function getFieldConf(){
		if(empty(self::$filed_conf)){
			self::$filed_conf = $this->loadData('fields');
		}
		return self::$filed_conf;
	}
	
	//获取字段类型配置文件
	public function getFieldTypeConf(){
		if(empty(self::$filed_type_conf)){
			self::$filed_type_conf = $this->loadData('fieldtype');
		}
		return self::$filed_type_conf;
	}
	
	//载入基本配置文件
	public function loadData($key=''){
		$data =array();
		$conf_file_path = DIR_ROOT.'/include/'.$key.'.conf.php';
		
		if (file_exists($conf_file_path)) {
			$data = include $conf_file_path;
		}
		return $data;
	}
	
	//根据组件名称获取组件
	public function getQualityByName($quality_name=''){
		$data = array();
		$widget_file = 'Quality'.ucfirst($quality_name)."Module";
		$widget_path = DIR_ROOT.'/modules/quality/'.$quality_name.'/'.$widget_file.".php";
                
                if (empty($quality_name)){
                    return null;
                }
                
		if(file_exists($widget_path)){
			require_once($widget_path);
			$tool = new $widget_file();
			$data = $tool->loadFields();
		}
		return $data;
	}
	
	//根据字段名获取字段配置属性
	public function getFieldByName($field_name=''){
		$field_conf = $this->getFieldConf();
		return $field_conf[$field_name];
	}
	
	//根据字段类型获取类型配置属性
	public function getFieldTypeByName($field_type_name=''){
		$field_type_conf = $this->getFieldTypeConf();
		return $field_type_conf[$field_type_name];
	}
	
	//生成单个组件的建表sql
	public function createQualityTableSql($Quality_name='',$fields,$table_suffix=''){
		if(!$Quality_name) return false;
		$table_prefix = 'quality';
		$table_name = $table_prefix."_".$Quality_name;
		
		$sql_start = "CREATE TABLE `".$table_name."` (
						`id`  int(11) NOT NULL AUTO_INCREMENT ,
						`user_id`  int(11) NOT NULL COMMENT '用户id' , ";
		
		if(!empty($table_suffix)){
			$table_name .= "_".$table_suffix;
			$sql_start = "CREATE TABLE `".$table_name."` (
						`id`  int(11) NOT NULL AUTO_INCREMENT ,
						`product_id`  int(11) NOT NULL COMMENT '产品id' , ";
		}
		
		//检测表是否存在
		$connection=Yii::app()->db;
		$command=$connection->createCommand("SHOW TABLES LIKE '".$table_name."'");
		if($command->execute()==1){
			return false;
		}
		
		$sql_body = $this->getSingleFieldSql($fields);
		
		$sql_end .= " `create_time`  int(11) NULL COMMENT '创建时间' ,
								`status`  tinyint(1) NULL DEFAULT 0 COMMENT '状态' ,
								
							PRIMARY KEY (`id`)
							
						)ENGINE=MyISAM; ";
		
		if($sql_body){
			$connection=Yii::app()->db;
			$command=$connection->createCommand($sql_start.$sql_body.$sql_end);
			$command->execute();
		}
		else
			return false;
	}
	
	//根据具体属性生成单条可执行sql
	public function getSingleFieldSql($fields=array()){
		if(!$fields) return false;
		
		$sql = '';
		foreach($fields as $k=>$v){
				$field = $this->getFieldByName($v['field']);	
				if(!empty($field)) 
					$field = array_merge($v,$field);
				else 
					return false;
				$field_sql = $this->fieldSqlRule($field);
				if($field_sql) $sql .= $field_sql;
		}
		
		return $sql;
	}
	
	//字段sql转换规则
	public function fieldSqlRule($field){
		//$fields_conf = Lib_Gtools_System::loadConf('global', 'fields'); // fields.conf.php
		$sql = '';
	
		if(!empty($field)) $field_type = $this->getFieldTypeByName($field['field_type']);
		
		if(empty($field)) return false;
		
		$name = $field['field_name'];
		$type = $field_type['type'];
		$field = array_merge($field,$field_type);
		$length = ($type!='decimal') ? $field['length'] : $field['length'].",".$field['point'];
		
		$default =is_null($field['default']) ?  'null' : $field['default'] ;
		$default = (is_null($field['default']) == false && $field['default']==='') ? "' '" : $default;
		$comment= $field['comment'] ? $field['comment'] : $field['label'];
		
		$char_set = $field_type['charset'] ? $field_type['charset'] : '';
		$null_set = $field_type['nullset'] ? $field_type['nullset'] : 'NULL';
		
		$sql .= " `".$name."` ".$type."(".$length.") ".$char_set." ".$null_set." DEFAULT ".$default." COMMENT '".$comment."', ";
		if($field['index'] == 'full')
			$sql .= "FULLTEXT INDEX `".$name."` (`".$name."`)  ,";
		return $sql;		
	}

	
	/**
	 * 录入单个组件数据
	 * @param quality_name 【string】组件名称
	 * @param params 【array】传入数据
	 * @return id 【int】记录id
	 */
	public function qualityDataEntry($quality_name='',$params=array()){
		$res = false;
		$insert = array();
		if(!$quality_name || (!$params['user_id'] && !$params['product_id'])) return false;
		$quality = $this->getQualityByName($quality_name);
		if($quality){
			//用户申请
			if($params['user_id']) {
				$table_name = "quality_".$quality_name;
				$insert['user_id'] = $params['user_id'];
				$res = $this->dataEntryProcessing($table_name,$quality['fields'], $params, $insert);
			}
			//产品要求
			if($params['product_id']){
				$table_name = "quality_".$quality_name."_required";
				$insert['product_id'] = $params['product_id'];
				$res = $this->dataEntryProcessing($table_name,$quality['required_fields'], $params, $insert);
			}
			return true;
		}else{
			return false;
		}
		
	}
	
	//组件数据入库操作
	public function dataEntryProcessing($table_name,$fields,$params,$insert){
		$flag = false;
		$insert['create_time'] = time();
		if(!empty($fields)){
			foreach ($fields as $k=>$v){
				if(isset($params[$v['field_name']])){
					$field_type = $this->getFieldByName($v['field']);
					if($field_type['field_type'] == 'string' && is_array($params[$v['field_name']])){	
						$insert[$v['field_name']] = implode(",",$params[$v['field_name']]);
					}else{
						$insert[$v['field_name']] = $params[$v['field_name']]; 
					}
				}
			}
		}
		$insert['status'] = 0;

		//检测是否已经录入
		$sql = "select * from ".$table_name;

		if($params['user_id']) $sql .= " where user_id=".$params['user_id'];
		if($params['product_id']) $sql .= " where product_id=".$params['product_id'];

		$connection=Yii::app()->db;
		$command=Yii::app()->db->createCommand($sql);
		$widget_data = $command->queryRow();

		if(empty($widget_data))
			return Yii::app()->db->createCommand()->insert($table_name,$insert);
		else{
			$update_sql = " status = 0 ";
			foreach ($insert as $k => $v) {
				if(empty($update_sql)) 
					$update_sql .= " ".$k."= '".$v."'";
				else
					$update_sql .= ", ".$k."= '".$v."'";
			}
			//echo "update ".$table_name." set ".$update_sql." where id=".$widget_data['id'];die;
			return Yii::app()->db->createCommand("update ".$table_name." set ".$update_sql." where id=".$widget_data['id'])->execute();
		}
	}
	
	//单个组件查询(根据用户查产品)
	public function widgetSearch($widget_name='',$params=array(),$target){
		$widget = $this->getQualityByName($widget_name);
		$sql_head = 'select DISTINCT('.$target.') from quality_'.$widget_name;
		if($target=='product_id') $sql_head .="_required ";
		$sql_body = '';
		foreach($widget['required_fields'] as $k=>$v){
			foreach(array_keys($params) as $kk=>$vv){
				if($target=='product_id' && $v['related_with'][$vv]){
					$type = $v['related_with'][$vv];
					if(!empty($params[$vv]) && $params[$vv] != 0.00) {
						$singleSql = $this->getSingleSearchSql($type,$v['field_name'],$params[$vv],$target);
						if(!empty($sql_body) && !empty($singleSql)) $sql_body .= " AND ".$singleSql;
						else $sql_body .= $singleSql;
					}
				}elseif($target=='user_id' && $v['field_name'] == $vv){
					foreach($v['related_with'] as $kkk=>$vvv){
						$singleSql = $this->getSingleSearchSql($vvv, $kkk, $params[$vv],$target);
						if(!empty($sql_body) && !empty($singleSql)) $sql_body .= " AND ".$singleSql;
						else $sql_body .= $singleSql;
					}
				}
			}
		}

		//if($widget_name=='person'){echo $sql_head." where ".$sql_body;die;}

		if(!empty($sql_body)){
			$connection=Yii::app()->db;
			//echo $sql_head." where ".$sql_body;die;
			$command=$connection->createCommand($sql_head." where status <> 1 and ".$sql_body);
			return $command->queryAll();
		}else{
			return $sql;
		}
	}
	
	//获取单个查询sql（根据用户查产品）
	public function getSingleSearchSql($logic,$field_name,$field_value,$target){
		$sql = '';
		if($target == 'user_id' && empty($field_value)) return $sql;
		switch($logic){
			case 'IN' :
				if($target == 'product_id') $sql = "(MATCH(".$field_name.") AGAINST('".$field_value.",0' in boolean MODE) )";
				else $sql = "(".$field_name." IN (".$field_value.") )";
				break;
			case 'EGT':
				if($target == 'product_id') $sql = "(".$field_name." <= ".$field_value.")";
				else $sql = "(".$field_name." >= ".$field_value.")";
				break;
			case 'ELT':
				if($target == 'product_id') $sql = "(".$field_name." >= ".$field_value.")";
				else $sql = "(".$field_name." <= ".$field_value.")";
				break;
			case 'NOTNULL':
				if($target == 'user_id') $sql = "(".$field_name." <> 0 or ".$field_name." IS NOT NULL or ".$field_name." <> '')";
				break;
		}
		return $sql;
	}
	
	
}