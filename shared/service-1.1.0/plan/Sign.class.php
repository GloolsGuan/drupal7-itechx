<?php
namespace service\plan;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use service\base\Base;
use service\plan\models\Signdata;
use \service\plan\models\Participant;

/**
 * Description of Schedule
 *
 * @author glools
 */
class Sign extends \service\base\Module
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
    public function addSigndata(array $data = [])
    {
        return \Yii::$app->db->createCommand()->insert(Signdata::tableName(), $data)->execute();
    }

    public function getSignList($map)
    {
        return signdata::find()->where($map)->asArray()->all();
    }

    public function getPlanParticipant($plan_id)
    {
        return Participant::find()->where(['plan_id'=>$plan_id])->asArray()->all();
    }

}
