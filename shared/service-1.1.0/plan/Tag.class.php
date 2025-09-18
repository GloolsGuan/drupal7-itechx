<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/24
 * Time: 16:36
 */

namespace service\plan;


use service\plan\models\PlanTag;
use service\plan\models\RsPlanTag;

/**
 *
 * 计划标签
 *
 * $sPlan = \Yii::loadService('plan');
 * $sTag = $sPlan->loadTag($plan_id);
 * $tags = ['tagA','tagB','tagC'];
 * $rs = $sTag->setTags($tags);
 * $tags = $sTag->listTags();
 *
 * Class Tag
 * @package service\plan
 */
class Tag extends \service\base\Module
{
    /**
     * @var \service\plan\Plan
     */
    public $context;
    public $plan = null;

    public function init()
    {
        parent::init();
    }

    /**
     * 设置标签
     *
     * @param array $tag_names ['tagA','tagB','tagC'....]
     * @return array|bool|\yii\db\ActiveRecord[] 成功时返回标签信息
     * @throws \yii\db\Exception
     */
    public function setTags($tag_names = [])
    {
        if (!is_array($tag_names)) $tag_names = [$tag_names];
        if (empty($tag_names)) return true;
        $ex_tags = PlanTag::find()->where(['tag_name' => $tag_names])->asArray()->all();
        $ex_tag_names = array_column($ex_tags, 'tag_name');
        $diff_names = array_diff($tag_names, $ex_tag_names);
        array_walk($diff_names, function (&$item) {
            $item = [$item];
        });
        if (!empty($diff_names)) {
            PlanTag::find()->createCommand()->batchInsert(PlanTag::tableName(), ['tag_name'], $diff_names)->execute();
        }
        if (false === RsPlanTag::deleteAll(['plan_id' => $this->plan->plan_id])) return $this->buildResponse('failed', 400, 'failed to delete plan tags');
        $tags = PlanTag::find()->where(['tag_name' => $tag_names])->asArray()->all();
        $data = [];
        array_walk($tags, function ($item) use (&$data) {
            $data[] = [$this->plan->plan_id, $item['plan_tag_id']];
        });
        if (false === RsPlanTag::find()->createCommand()->batchInsert(RsPlanTag::tableName(), ['plan_id', 'plan_tag_id'], $data)->execute()) return $this->buildResponse('failed', 400, 'failed to set plan tags');
        return $tags;
    }

    /**
     * 删除标签
     *
     * @param array $tag_ids 标签ID
     * @param bool $strict
     * @return bool|\service\base\type
     */
    public function deleteTags(array $tag_ids = [], $strict = false)
    {
        if (false === RsPlanTag::deleteAll(['plan_id' => $this->plan->plan_id, 'plan_tag_id' => $tag_ids])) return $this->buildResponse('failed', 400, 'failed to delete plan tags');
        if ($strict) {
            if (false === PlanTag::deleteAll(['plan_tag_id' => $tag_ids])) return $this->buildResponse('failed', 400, 'failed to delete tag strickly');
        }
        return true;
    }

    /**
     * 枚举所有标签
     *
     * @return \service\base\type
     */
    public function listTags()
    {
        $tags = PlanTag::find()->from(['t' => PlanTag::tableName()])
            ->select(['t.*', 'rs.*'])
            ->join('INNER JOIN', RsPlanTag::tableName() . ' AS rs', 'rs.plan_tag_id = t.plan_tag_id')
            ->where(['plan_id' => $this->plan->plan_id])
            ->asArray()->all();
        if (false === $tags) return $this->buildResponse('failed', 400, 'failed to get tags');
        return $tags;
    }

}