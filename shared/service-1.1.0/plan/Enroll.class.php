<?php


namespace service\plan;

use service\base\Base;
use service\base\Error;
use service\plan\models\Enroll as ModEnroll;
use service\plan\models\SignDetail;
use service\task\ExaminationTask;
use service\plan\models\Plan as PlanModel;
use service\plan\models\Participant;
use service\base\Module;
use service\plan\interfaces\Enroll as EnrollInterface;

/**
 * Class Enroll
 * @package service\plan
 * @author liurw 20161116
 */
class Enroll extends Module implements EnrollInterface
{
    public function instance()
    {
        $model = new ModEnroll();
        $model->plan_id = 0;
        $model->save(false);
        return $model->getAttributes();
    }

    /**
     * 添加
     * @param array $data
     * @return array|bool
     */
    public function create(array $data)
    {
        if (false === ($sign = ModEnroll::createItem($data)))
            return $this->buildResponse('error', 400, 'failed to create enroll');

        return $sign->getAttributes();
    }

    /**
     * 添加/更新详情
     * @param array $data
     * @return array|bool
     */
    public function saveEnroll(array $data)
    {
        $enroll_o = ModEnroll::findOne($data['id']);

        $enroll_o->setAttributes($data);


        if (true === $enroll_o->save()) return $this->buildResponse('success', 201, $enroll_o->getAttributes());
        return $this->buildResponse('error', 400, $enroll_o->getFirstErrors());
    }

    public function getItemByPlanId($plan_id)
    {

        return ModEnroll::getItemByPlanId($plan_id);
    }

    public function getPlanByAppid($appid)
    {
        $where = ['application_id' => $appid, 'plan_status' => PlanModel::STATUS_PUBLISHED];
        if (false === ($plan = PlanModel::find()->where($where)->asArray()->all())) return [1];
        return $plan;
    }

    public function getCourse($where, $offset = 0, $limit = 20, $order = '')
    {
        $query = ModEnroll::find()->from(['en' => ModEnroll::tableName()])
            ->select([
                'plan.plan_poster as lessonPoster',
                'plan.plan_name as lessonTitle',
                'plan.plan_bt as lessonBeginTime',
                'en.cash_price as lessonPrice',
                'plan.plan_id as lessonId',
                'en.limited as limitMaxPer',
                'participant.total as joinPerson'
            ])
            ->where($where)
            ->join('LEFT JOIN', PlanModel::tableName() . ' plan', 'en.plan_id = plan.plan_id')
            ->join('LEFT JOIN', '(SELECT COUNT(*) AS total,plan_id FROM ' . Participant::tableName() . ' ) AS participant', 'participant.plan_id = en.plan_id');

        if (!empty($order)) {
            $enrolls = $query->offset($offset)->limit($limit)->orderBy($order)->asArray()->all();
        } else {
            $enrolls = $query->offset($offset)->limit($limit)->orderBy('plan_ct desc')->asArray()->all();
        }
        $count = $query->count();
        return ['lessonList' => $enrolls, 'lessonTotal' => $count];
    }

    public function test($test)
    {
        return 'test';
    }

    public function listPlans($name = '', $sort = '', $page = 1, $page_size = 20)
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
                'participant.total as joinPerson'
            ])
            ->where($where)
            ->join('LEFT JOIN', PlanModel::tableName() . ' plan', 'en.plan_id = plan.plan_id')
            ->join('LEFT JOIN', '(SELECT COUNT(*) AS total,plan_id FROM ' . Participant::tableName() . ' ) AS participant', 'participant.plan_id = en.plan_id');

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

        $courses = PlanModel::find()->from(['plan' => PlanModel::tableName()])
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
}
