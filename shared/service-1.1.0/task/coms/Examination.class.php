<?php


namespace service\task;


use service\base\Base;
use service\base\Error;
use service\task\models\ExaminationTask as ModExaminationTask;
use service\task\models\Task;

class Examination extends AbstractCom
{

    public $sExamination;

    public function init()
    {
        $this->sExamination = Base::loadService('\service\examination\Examination');
        if (false === \service\examination\Examination::$is_supported_private_entity) return $this->buildResponse('error', 400, 'service examination does not support private entity');
    }

    public function create(array $data = [])
    {

    }

    public function remove(array $ids = [])
    {

    }

    public function getEntities(array $ids = [])
    {

    }

    public function update(array $data = [])
    {

    }
}
























