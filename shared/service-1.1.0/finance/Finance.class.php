<?php


namespace service\finance;

use \service\base\Base;
use service\base\Error;
use \service\finance\models\Finance as ModFinance;
use service\task\ExaminationTask;

class Finance extends \service\base\Module
{

	public function __construct($id, $parent = null, $config = [])
    {
        parent::__construct([]);
    }

    public static function getItems(array $where, $offset = -1, $limit = -1)
    {
		return ModFinance::getItems($where, $offset, $limit);
	}
	
	public static function getAllItems(array $where, $offset = -1, $limit = -1,$order='')
	{
		return ModFinance::getAllItems($where, $offset, $limit,$order);
	}
	public static function setStatus(array $data)
    {
		$finance_o = ModFinance::findOne($data['id']);
		 
        $finance_o->setAttributes($data);
			
         if (true === $finance_o->save()) return $finance_o->getAttributes();
         return false;
	}

}

































