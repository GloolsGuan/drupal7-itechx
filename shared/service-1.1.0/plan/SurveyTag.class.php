<?php
/**
 *
 */

namespace service\plan;

use service\base\Module;
use service\plan\interfaces\SurveyTag as SurveyTagInterface;
use service\plan\models\PlanSurveyTag as PlanSurveyTagModel;

/**
 * Class SurveyTag
 * @package service\plan
 * @author yangzy 20161213
 */
class SurveyTag extends Module implements SurveyTagInterface
{
    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        if (false === $item = PlanSurveyTagModel::createItem($data))
            return $this->buildResponse('failed', 400, 'failed to create tag');
        return $this->buildResponse('success', 201, $item->getAttributes());
    }

    /**
     * @inheritDoc
     */
    public function delete(array $ids)
    {
        if (false === PlanSurveyTagModel::deleteItemsById($ids))
            return $this->buildResponse('failed', '400', 'failed to delete tag');
        return $this->buildResponse('success', 201, $ids);
    }

    /**
     * @inheritDoc
     */
    public function update($id, array $data)
    {
        if (false === $item = PlanSurveyTagModel::getItemById($id))
            return $this->buildResponse('failed', 400, 'failed to update tag infomation');

        $item->setAttributes($data);
        if (false === $item->save())
            return $this->buildResponse('failed', 400, 'failed to update tag infomation');

        return $this->buildResponse('success', 201, $item->getAttributes());
    }

    /**
     * @inheritDoc
     */
    public function getTags(array $ids)
    {
        if (false === $items = PlanSurveyTagModel::getItemsByid($ids))
            return $this->buildResponse('failed', 400, 'failed to get tags');
        return $this->buildResponse('success', 201, $items);
    }

    /**
     * @inheritDoc
     */
    public function listTags(array $map = [], $offset = -1, $limit = -1)
    {
        if (false === $items = PlanSurveyTagModel::getItems($map, $offset, $limit))
            return $this->buildResponse('failed', 400, 'failed to list tags');
        return $this->buildResponse('success', 201, $items);
    }

    /**
     * @inheritDoc
     */
    public function listPlanTags($planId, array $map = [], $offset = -1, $limit = -1)
    {
        return $this->listTags(array_merge($map, ['plan_id' => $planId]));
    }
}