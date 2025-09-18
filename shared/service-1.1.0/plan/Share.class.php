<?php
/**
 *
 */

namespace service\plan;

use service\base\Base;
use service\base\Module;
use service\plan\interfaces\Share as ShareInterface;
use service\plan\models\Share as ShareModel;
use yii\helpers\ArrayHelper;

class Share extends Module implements ShareInterface
{
    /**
     * @var \service\user\User
     */
    protected $userService;

    /**
     * @var \service\plan\Plan
     */
    protected $planService;

    public function init()
    {
        parent::init();

        $this->userService = Base::loadService('\service\user\User');
        $this->planService = Base::loadService('\service\plan\Plan');
    }

    /**
     * @inheritDoc
     */
    public function search(array $search = [], $offset = -1, $limit = -1)
    {
        if (false === $items = ShareModel::getItems($search, $offset, $limit))
            return $this->buildResponse('failed', 400, 'failed to search share');

        $items = $this->fillSharesData($items);

        return $this->buildResponse('success', 201, $items);
    }

    /**
     * @inheritDoc
     */
    public function getShares(array $ids)
    {
        $items = ShareModel::getItems([ShareModel::primaryKey()[0] => $ids]);
        if (false === $items) return $this->buildResponse('error', 400, 'failed to get share');
        $items = $this->fillSharesData($items);
        return $this->buildResponse('success', 201, $items);
    }

    /**
     * @inheritDoc
     */
    public function createShare(array $shareData)
    {
        $item = new ShareModel();
        $item->setAttributes($shareData);
        if (false === $item->save()) return $this->buildResponse('failed', 400, 'failed to create share');
        $items[] = $item->getAttributes();
        $items = $this->fillSharesData($items);
        return $this->buildResponse('success', 201, array_pop($items));
    }

    /**
     * @inheritDoc
     */
    public function deleteShares(array $ids)
    {
        if (false === ShareModel::deleteItems([ShareModel::primaryKey()[0] => $ids]))
            return $this->buildResponse('failed', 400, 'failed to delete share');
        return $this->buildResponse('success', 200, $ids);
    }

    protected function fillSharesData($shares)
    {
        if (is_array($shares)) {
            $planIds = ArrayHelper::getColumn($shares, 'plan_id');
            $groupIds = ArrayHelper::getColumn($shares, 'group_id');
            $unique_codes = ArrayHelper::getColumn($shares, 'unique_code');

            $plansData = $this->planService->getPlansById($planIds);
            $plansData = ArrayHelper::index($plansData['data'], 'plan_id');

            $usersData = $this->userService->loadListByCodes($unique_codes);
//            $usersData = ['data' => []]; //debug
            $usersData = ArrayHelper::index($usersData['data'], 'unique_code');

            foreach ($shares as &$share) {
                $plan = ArrayHelper::getValue($plansData, $share['plan_id'], []);
                $user = ArrayHelper::getValue($usersData, $share['unique_code'], []);
                $share['planTitle'] = ArrayHelper::getValue($plan, 'plan_name', '');
                $share['beginTime'] = ArrayHelper::getValue($plan, 'plan_bt', '');
                $share['endTime'] = ArrayHelper::getValue($plan, 'plan_et', '');
                $share['userName'] = ArrayHelper::getValue($user, 'user_name', '');
                $share['phone'] = ArrayHelper::getValue($user, 'safe_mobile', '');
            }

            return $shares;
        }
        return $shares;
    }
}