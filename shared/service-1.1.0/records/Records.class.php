<?php


namespace service\Records;

use \service\base\Base;
use service\base\Error;
use \service\finance\models\Finance as ModFinance;
use \service\refund\models\Refund as ModRefund;
use  \service\plan\models\Plan as ModPlan;

class Records extends \service\base\Module
{

	public function __construct($id, $parent = null, $config = [])
    {
        parent::__construct([]);
    }

    public static function getItems(array $where, $offset = -1, $limit = -1)
    {
		return ModRefund::getItems($where, $offset, $limit);
	}
	public function getData($type,array $map,$offset=-1,$limit=-1)
    {

		$shouru=['money'=>0];
		$zhichu=['money'=>0];
		$shourudb=[];
		$zhichudb=[];
		switch($type)
		{
			case '':
			  $where=[];
			  $where['status']=1;
			  $shouru=ModFinance::find()->select(['money'=>'SUM(amount)'])->where($where)->asArray()->one();
			  //var_dump($shouru);
			  unset($where);
			  $where=[];
			  $where['status']=1;
			  $zhichu=ModRefund::find()->select(['money'=>'SUM(amount)'])->where($where)->asArray()->one();
			  //var_dump($zhichu);
			  
			  unset($where);
			  $where=[];
			  $where['status']=1;
			  $zhichudb=ModRefund::find()->select(['id','create_time as pay_time','amount as money','unique_code','p.plan_name as course','type'])
			  ->where($where)
			  ->join('LEFT JOIN', ModPlan::tableName() . ' AS p', 'biz_refund.course_id = p.plan_id');

			  
			  unset($where);
			  $where=[];
			  $where['status']=1;
			  $shouru=ModFinance::find()->select(['money'=>'SUM(amount)'])->where($where)->asArray()->one();
			  
			  $shourudb=ModFinance::find()->select(['id','pay_time','amount as money','unique_code','p.plan_name as course','type'])
			  ->where($where)
			  ->join('LEFT JOIN', ModPlan::tableName() . ' AS p', 'biz_order.course_id = p.plan_id');

			  $zhichudb=$zhichudb->union($shourudb)->asArray()
			  ->all();

				$unique_code=[];
				foreach($zhichudb as $key=>&$val)
				{
					$unique_code[]=$val['unique_code'];
				}
				if(sizeof($unique_code)>0)
				{

					$suser = \Yii::loadService('user');
					$userlist=$suser->loadListByCodes($unique_code);

					$userlist = array_combine(array_column($userlist['data'], 'unique_code'), array_column($userlist['data'], 'user_name'));

					foreach($zhichudb as $key=>&$val)
					{
						$val['student']=$userlist[$val['unique_code']];
					}
				}


			  $shourudb=[];
			  //var_dump($zhichudb);
			  break;
			case '0':
			  $where=[];
			  $where['status']=1;
			  $zhichu=ModRefund::find()->select(['money'=>'SUM(amount)'])->where($where)->asArray()->one();
			  //var_dump($zhichu);
			  $zhichudb=ModRefund::find()->select(['id','create_time as pay_time','amount as money','unique_code','p.plan_name as course','type'])
			  ->where($where)
			  ->join('LEFT JOIN', ModPlan::tableName() . ' AS p', 'biz_refund.course_id = p.plan_id')
			  ->asArray()
			  ->all();
			  if (-1 != $offset) $query->offset($offset);

              if (-1 != $limit) $query->limit($limit);
			  
			  $unique_code=[];
		      foreach($zhichudb as $key=>&$val)
		      {			
			    $unique_code[]=$val['unique_code'];
				$val['type']="支出";
		      }
		      if(sizeof($unique_code)>0)
		      {
 
		        $suser = \Yii::loadService('user');
		        $userlist=$suser->loadListByCodes($unique_code);
		    
		        $userlist = array_combine(array_column($userlist['data'], 'unique_code'), array_column($userlist['data'], 'user_name'));
		    
		        foreach($zhichudb as $key=>&$val)
		        {
                   $val['student']=$userlist[$val['unique_code']];
		        }
		      }
			  
			  //var_dump($zhichudb);
			  break;
			case '1':
			  $where=[];
			  $where['status']=1;
			  $shouru=ModFinance::find()->select(['money'=>'SUM(amount)'])->where($where)->asArray()->one();
			  
			  $shourudb=ModFinance::find()->select(['qq'=>'newfield','id','pay_time','amount as money','unique_code','p.plan_name as course','type'])
			  ->where($where)
			  ->join('LEFT JOIN', ModPlan::tableName() . ' AS p', 'biz_payment.course_id = p.plan_id')
			  ->asArray()
			  ->all();
			  if (-1 != $offset) $query->offset($offset);

              if (-1 != $limit) $query->limit($limit);
			  
			  $unique_code=[];
		      foreach($shourudb as $key=>&$val)
		      {			
			    $unique_code[]=$val['unique_code'];
				$val['type']="收入";
		      }
		      if(sizeof($unique_code)>0)
		      {
 
		        $suser = \Yii::loadService('user');
		        $userlist=$suser->loadListByCodes($unique_code);
		    
		        $userlist = array_combine(array_column($userlist['data'], 'unique_code'), array_column($userlist['data'], 'user_name'));
		    
		        foreach($shourudb as $key=>&$val)
		        {
                   $val['student']=$userlist[$val['unique_code']];
		        }
		      }
			  
			  //var_dump($shourudb);
			  break;			  
		}
		

		return ['shouru'=>$shouru,'zhichu'=>$zhichu,'shourudb'=>$shourudb,'zhichudb'=>$zhichudb,'res'=>array_merge($shourudb,$zhichudb)];
	}
	
}































