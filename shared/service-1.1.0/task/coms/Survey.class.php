<?php


namespace service\task;


use service\base\Base;

class Survey extends AbstractCom
{

    public $sSurvey;

    public function init()
    {
        $this->sSurvey = Base::loadService('\service\survey\Survey');
        if (false === \service\survey\Survey::$is_supported_private_entity) return $this->buildResponse('error', 400, 'service survey does not support private entity');
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