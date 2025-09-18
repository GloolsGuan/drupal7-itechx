<?php
namespace service\plan;

use service\base\Base;
use \service\base\Module;
use service\plan\models\PlanSurvey as PlanSurveyModel;
use service\plan\models\PlanSurveyTag as PlanSurveyTagModel;
use service\plan\models\RsPlanSurveyTag as RsPlanSurveyTagModel;
use service\plan\interfaces\Survey as SurveyInterface;
use yii\helpers\ArrayHelper;

/**
 * Class Survey
 * @package service\plan
 */
class Survey extends Module implements SurveyInterface
{

    /**
     * 获取计划所关联的调研信息
     * @deprecated
     * @param $plan_id
     * @return array
     */
    public function getPlanSurveys($plan_id)
    {
        $surveys = PlanSurveyModel::getItems(['plan_id' => $plan_id]);

        if (false === $surveys)
            return $this->buildResponse('failed', 400, 'failed to get surveys');

        return $surveys;
    }

    /**
     * @deprecated
     */
    public function getSurveyLocked($plan_id)
    {
        return PlanSurveyModel::getSurveyLocked($plan_id);
    }

    /**
     * @deprecated
     */
    public function savePlanSurvey($plan_id, $survey_id)
    {
        return PlanSurveyModel::savePlanSurvey($plan_id, $survey_id);
    }

    /**
     * @inheritDoc
     * @author yangzy 20161212
     */
    public function getSurveys($planId, array $map = [], $offset = -1, $limit = -1)
    {
        $map['survey.plan_id'] = $planId;

        $query = PlanSurveyModel::find()->from(['survey' => PlanSurveyModel::tableName()]);

        $query->where($map);

        $query->leftJoin(['rs' => RsPlanSurveyTagModel::tableName()], 'rs.plan_survey_id=survey.id');
        $query->leftJoin(['tag' => PlanSurveyTagModel::tableName()], 'tag.id=rs.tag_id');

        if (-1 != $offset) $query->offset($offset);
        if (-1 != $limit) $query->limit($limit);

        $items = $query->asArray()->all();

        if (false === $items)
            return $this->buildResponse('failed', 400, 'failed to get surveys');

        $sids = ArrayHelper::getColumn($items, 'survey_id');

        $limeSurveyService = Base::loadService('service\survey\Lime');

        $surveys = $limeSurveyService->getSurveysById($sids);

        if ($this->isErrorResponse($surveys))
            return $this->buildResponse($surveys['status'], $surveys['code'], $surveys['data']);

        $surveys = $surveys['data'][0];
        $surveys = ArrayHelper::index($surveys, 'sid');

        foreach ($items as &$item) {
            $item = ArrayHelper::merge(ArrayHelper::getValue($surveys, $item['survey_id']), $item);
        }
        return $this->buildResponse('success', 201, $items);
    }

    /**
     * @inheritDoc
     * @author yangzy 20161212
     */
    public function getSurveySetting($planSurveyId)
    {
        if (false === $item = PlanSurveyModel::getItemByid($planSurveyId))
            return $this->buildResponse('failed', 400, 'failed to get setting');
        return $this->buildResponse('success', 201, $item->getAttributes());
    }

    /**
     * @inheritDoc
     * @author yangzy 20161212
     */
    public function setSurveySetting($planSurveyId, array $setting)
    {
        if (false === $item = PlanSurveyModel::getItemByid($planSurveyId))
            return $this->buildResponse('failed', 400, 'failed to get setting');
        $item->setAttributes($setting);
        if (false === $item->save())
            return $this->buildResponse('failed', 400, 'failed to save setting');
        return $this->buildResponse('success', 201, $item->getAttributes());
    }

    /**
     * @inheritDoc
     * @author yangzy 20161212
     */
    public function addSurveys($planId, array $surveyIds)
    {
        $data = [];
        $now = date('Y-m-d H:i:s', time());
        foreach ($surveyIds as $surveyId) {

            $data[] = [$planId, $surveyId, PlanSurveyModel::STATUS_NORMAL, $now];
        }
        $res = PlanSurveyModel::find()->createCommand()->batchInsert(PlanSurveyModel::tableName(), ['plan_id', 'survey_id', 'status', 'created_at'], $data)->execute();

        if (false == $res)
            return $this->buildResponse('failed', 400, 'failed to add surveys');

        return $this->buildResponse('success', 201, [$planId, $surveyIds]);
    }

    /**
     * @inheritDoc
     * @author yangzy 20161212
     */
    public function removeSurveys(array $planSurveyIds)
    {
        if (false === PlanSurveyModel::deleteItemsById($planSurveyIds))
            return $this->buildResponse('failed', 400, 'failed to delete remove plan surveys');
        return $this->buildResponse('success', 201, $planSurveyIds);
    }

    /**
     * @inheritDoc
     * @author yangzy 20161212
     */
    public function setSurvey($planSurveyId, array $planSurveyInfo)
    {
        if (false === $item = PlanSurveyModel::getItemById($planSurveyId))
            return $this->buildResponse('failed', 400, 'failed to get plan survey');

        $item->setAttributes($planSurveyInfo);

        if (false === $item->save())
            return $this->buildResponse('failed', 400, 'failed to update plan survey data');

        return $this->buildResponse('success', 201, $item->getAttributes());
    }

    /**
     * @inheritDoc
     * @author yangzy 20161213
     */
    public function setTags($planSurveyId, array $tagIds = [])
    {
        if (false === RsPlanSurveyTagModel::deleteAll(['plan_survey_id' => $planSurveyId]))
            return $this->buildResponse('failed', 400, 'failed to update tags');

        if (!empty($tagIds)) {
            $keys = ['plan_survey_id', 'tag_id'];
            $data = [];
            foreach ($tagIds as $tagId) {
                $data[] = [$planSurveyId, $tagId];
            }

            $res = RsPlanSurveyTagModel::find()->createCommand()->batchInsert(RsPlanSurveyTagModel::tableName(), $keys, $data)->execute();
            if (false === $res)
                return $this->buildResponse('failed', 400, 'failed to update tags');
        }

        $tags = $this->getSurveyTags($planSurveyId);
        if ($this->isErrorResponse($tags))
            return $tags;

        return $this->buildResponse('success', 201, $tags['data']);
    }

    /**
     * @inheritDoc
     * @author yangzy 20161213
     */
    public function getSurveyTags($planSurveyId)
    {
        if (false === $item = PlanSurveyModel::getItemById($planSurveyId))
            return $this->buildResponse('failed', 400, 'plan\'s survey was not found');

        $tags = $item
            ->hasMany(PlanSurveyTagModel::className(), ['id' => 'tag_id'])
            ->viaTable(RsPlanSurveyTagModel::tableName(), ['plan_survey_id' => 'id'])
            ->asArray()->all();

        if (false === $tags)
            return $this->buildResponse('failed', 400, 'failed to get survey\'s tags');

        foreach ($tags as &$tag) $tag['plan_survey_id'] = $planSurveyId;

        return $this->buildResponse('success', 201, $tags);
    }


}