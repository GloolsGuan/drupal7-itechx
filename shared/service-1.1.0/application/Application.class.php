<?php
namespace service\application;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use service\base\Base;
use service\application\models\Application as ModApplication;

/**
 * Description of Schedule
 *
 * @author glools
 */
class Application extends \service\base\Module
{
    //put your code here

    public function init()
    {
        parent::init();
    }

    /**
     * 添加签到数据
     * @param Participant $participant
     * @return bool
     */
    public function getApplication($id)
    {
		return ModApplication::getApplication($id);
    }

    public function saveMenu($id, $data)
    {
        $app = ModApplication::findOne($id);
        $app->menu = json_encode($data);
        return $app->save();
    }
}
