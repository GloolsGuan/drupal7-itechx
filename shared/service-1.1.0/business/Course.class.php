<?php
/**
 * Created by PhpStorm.
 * User: Win10
 * Date: 2016/11/18
 * Time: 16:24
 */

namespace service\business;

use \service\base\Module;
use \service\business\models\Cart AS ModCart;
use \service\plan\models\Plan;
use \service\business\models\Enroll AS ModEnroll;
use \service\plan\models\Participant;


/**
 * @deprecated 已过期
 * @package service\plan\interfaces
 * @author zhuzf 20161124
 */
class Course extends Module
{
    public function getPlanByAppid($appid)
    {
        $where = ['application_id' => $appid, 'plan_status' => Plan::STATUS_PUBLISHED];
        if (false === ($plan = Plan::find()->where($where)->asArray()->all())) return [1];
        return $plan;
    }

    /**
     * ['AND',['participant.role'=>'PlanStudent'],['<>','participant.status','-1']]  查询participant表，得到参加培训人员数
     * @param $map
     * @param string $name
     * @param string $sort
     * @param int $page
     * @param int $page_size
     * @return array
     */
    public function listPlans($map, $name = '', $sort = '', $page = 1, $page_size = 20)
    {

        $where = [];
        if ($name) {
            $where = ['like', 'plan.plan_name', $name];
        }
        $query = ModEnroll::find()->from(['en' => ModEnroll::tableName()])
            ->select([
                'plan.plan_poster as lessonPoster',
                'plan.plan_name as lessonTitle',
                'plan.plan_bt as lessonBeginTime',
                'en.cash_price as lessonPrice',
                'plan.plan_id as lessonId',
                'en.limited as limitMaxPer',
                'count(participant.unique_code) as joinPerson'
            ])
            ->where($where)
            ->andWhere($map)
            ->andWhere(['>','plan.plan_bt',date('Y-m-d H:i:s')])
            ->andWhere(['AND',['participant.role'=>'PlanStudent'],['<>','participant.status','-1']])
            ->join('LEFT JOIN', Plan::tableName() . ' plan', 'en.plan_id = plan.plan_id')
            ->join('LEFT JOIN', Participant::tableName() . ' AS participant', 'participant.plan_id = en.plan_id');

        if ($sort == 'price') {
            $enrolls = $query->offset(($page - 1) * $page_size)->limit($page_size)->orderBy('en.cash_price asc')->asArray()->all();
        } elseif ($sort == '-price') {
            $enrolls = $query->offset(($page - 1) * $page_size)->limit($page_size)->orderBy('en.cash_price desc')->asArray()->all();
        } elseif ($sort == 'data') {
            $enrolls = $query->offset(($page - 1) * $page_size)->limit($page_size)->orderBy('plan.plan_bt asc')->asArray()->all();
        } elseif ($sort == '-data') {
            $enrolls = $query->offset(($page - 1) * $page_size)->limit($page_size)->orderBy('plan.plan_bt desc')->asArray()->all();
        } else {
            $enrolls = $query->offset(($page - 1) * $page_size)->limit($page_size)->orderBy('plan_ct desc')->asArray()->all();
        }
        $count = $query->count();
        return ['lessonList' => $enrolls, 'lessonTotal' => $count];
    }

    public function getCourseByIds($ids = [])
    {
        if (empty($ids)) return $this->buildResponse('error', 400, 'id cannot be empty');

        $courses = Plan::find()->from(['plan' => Plan::tableName()])
            ->select(['plan.*', 'enroll.*'])
            ->where(['in', 'plan.plan_id', $ids])
            ->join('LEFT JOIN', ModEnroll::tableName() . ' AS enroll', 'plan.plan_id = enroll.plan_id')
            ->asArray()
            ->all();

        if (false === $courses)
            return $this->buildResponse('failed', 400, 'failed to load course');
        else
            return $this->buildResponse('success', 200, $courses);
    }

    public function getCourseByid($id)
    {
        if (empty($id)) return $this->buildResponse('error', 400, 'id cannot be empty');

        $course = Plan::find()->from(['plan' => Plan::tableName()])
            ->select(['plan.*', 'enroll.*'])
            ->where(['plan.plan_id' => $id])
            ->join('LEFT JOIN', ModEnroll::tableName() . ' AS enroll', 'plan.plan_id = enroll.plan_id')
            ->asArray()
            ->one();
        if (false == $course) return $this->buildResponse('failed', 201, 'failed to load course');

        return $this->buildResponse('success', 200, $course);
    }

    public function getPlanParticipnat($id)
    {
        if (empty($id)) return $this->buildResponse('error', 400, 'id cannot be empty');

        $query = Participant::find()->where(['plan_id' => $id])->asArray();

        return ['participants' => $query->all(), 'total' => $query->count()];


    }
}