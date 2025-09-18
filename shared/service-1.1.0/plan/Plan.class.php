<?php
namespace service\plan;

use service\base\Base;
use service\plan\interfaces\Plan as PlanInterface;
use service\plan\interfaces\PlanBusiness as PlanBusiness;
use service\plan\models\Application as ModApplication;
use service\plan\models\Participant;
use service\plan\models\Plan as ModPlan;
use service\plan\models\RsPlanResource;
use service\plan\models\RsPlanResourceUsers;
use service\plan\models\RsTraineeGroup;
use service\plan\models\Schedule;
use service\plan\models\Train;
use service\plan\models\TraineeGroup;
use yii\db\ActiveRecord;
use \service\enroll\models\Enroll as ModEnroll;

class Plan extends \service\base\Module implements PlanBusiness, PlanInterface {
	public function __construct($id, $parent = null, $config = []) {
		parent::__construct([]);
	}

	/**
	 * 获取计划列表
	 * @param array $map
	 * @param int $offset
	 * @param int $limit
	 * @return array|\yii\db\ActiveRecord[]
	 */
	public function getPlans($map = [], $offset = 0, $limit = 20, $manager = '') {
		if (!is_array($map)) {
			return $this->buildResponse('error', 400, 'map must be array');
		}

		$map = [$map];

		array_unshift($map, 'AND');

		if ('' != $manager) {
			$map[] = ['like', 'name', $manager];
			$map[] = ['role' => Participant::TYPE_MANAGER];
		}

		$plans = ModPlan::getPlans($map, $offset, $limit);
		foreach ($plans[0] as &$plan) {
			$plan['managers'] = [];
		}

		if (!empty($plans[0])) {

			$plan_ids = array_column($plans[0], 'plan_id');

			$members = Participant::getMembers(['plan_id' => $plan_ids, 'role' => Participant::TYPE_MANAGER]);

			array_walk($members, function ($member, $key) use ($plan_ids, &$plans) {
				$index = array_search($member['plan_id'], $plan_ids);
				if (false !== $index) {
					$plans[0][$index]['managers'][] = $member;
				}
			});

		}

		return $plans;
	}

	public function loadPlans($conditions = [], $page = 1, $perpage = 12) {

		$offset = ($page - 1) * $perpage;
		//\GtoolsDebug::testLog(__METHOD__, [$offset, $page]);
		$plans = ModPlan::find()->where($conditions)->limit($perpage)->offset($offset)
			->indexBy('plan_id')->orderBy('plan_id desc')
			->asArray()->all();
		$total_result = ModPlan::find()->select('count(plan_id) as total')->where($conditions)->asArray()->one();

		//\GtoolsDebug::testLog(__METHOD__, $total);

		if (!empty($plans)) {
			foreach ($plans as $plan_id => $plan) {
				$plans[$plan_id]['members'] = models\Participant::find()->where(['plan_id' => $plan_id])->asArray()->all();
				$plans[$plan_id]['schedules'] = models\Schedule::find()->where(['plan_id' => $plan_id])->asArray()->all();
			}
			return $this->buildResponse('success', 201, [$total_result['total'], $plans]);
		}

		return $this->buildResponse('success', 200, []);
	}

	/**
	 * 计划列表搜索
	 * @param array $map
	 * @param int $offset
	 * @param int $limit
	 * @return array|\yii\db\ActiveRecord[]
	 * @author drce 20161128
	 */
	public function searchPlans($map = [], $sort = '', $offset = 0, $limit = 20) {
		if (!is_array($map)) {
			return $this->buildResponse('error', 400, 'map must be array');
		}

		$map = [$map];

		array_unshift($map, 'AND');

		//可被排序的字段，-date,date:负数为降序
		$can_sort = ['date' => 'p.update_time', '-date' => 'p.update_time',
			'type' => 'p.plan_type', '-type' => 'p.plan_type'];

		if (!empty($sort)) {
			$s = explode(',', $sort);
			foreach ($s as $k => $v) {
				if (array_key_exists($v, $can_sort)) {
					$map['_sort'][] = 0 === strpos($v, '-') ? $can_sort[$v] . ' DESC' : $can_sort[$v] . ' ASC';
				}
			}
		}

		$plans = ModPlan::getPlans($map, $offset, $limit);

		return $plans;
	}

	/**
	 * @inheritDoc
	 */
	public function getPlansById(array $planIds = []) {
		if (false === $items = Train::getItemsByid($planIds)) {
			return $this->buildResponse('failed', 400, 'failed to get plans');
		}

		return $this->buildResponse('success', 201, $items);
	}

	/**
	 * 获取计划详情
	 * @param $plan_id
	 * @return array
	 */
	public function getPlan($plan_id) {
		$plan = ModPlan::getPlanById($plan_id, true);

		if (empty($plan)) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		$plan['managers'] = Participant::getPlanManagers($plan_id);

		return $plan;
	}

	/**
	 * 发布计划
	 * @param $plan_id
	 * @return bool|\service\base\type
	 */
	public function publishPlan($plan_id) {
		if (null == ($plan = ModPlan::getPlanById($plan_id))) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		//TODO 检查状态是否可以发布：未发布且未开始

		$plan->plan_status = ModPlan::STATUS_PUBLISHED;
		$result = $plan->save();

		if (false == $result) {
			//\GtoolsDebug::testLog(__METHOD__, [$plan, $plan->getErrors()]);
			return $this->buildResponse('failed', 500, $plan->getErrors());
		}

		return $this->buildResponse('success', 201, $result);
	}

	/**
	 * 取消发布
	 * @param $plan_id
	 * @return bool|\service\base\type
	 */
	public function unPublishPlan($plan_id) {
		if (null == ($plan = ModPlan::getPlanById($plan_id))) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		//TODO 检查状态是否可以取消：已发布且未开始

		$plan->plan_status = ModPlan::STATUS_NORMAL;
		return $plan->save();
	}

	/**
	 * 取消(删除)计划
	 * @param $plan_id
	 * @return bool|\service\base\type
	 */
	public function cancelPlan($plan_id) {
		if (null == ($plan = ModPlan::getPlanById($plan_id))) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		$plan->plan_status = ModPlan::STATUS_DELETED;
		return $plan->save();
	}

	public function instanceEmptyModel() {
		return new ModPlan();
	}

	/**
	 * @Author GloolsGuan
	 * @param type $plan
	 */
	public function createPlan($plan) {
		$plan_result = $this->savePlan($plan);
		if (201 !== $plan_result['code']) {
			return $plan_result;
		}

		$plan_id = $plan_result['data']['plan_id'];
		if (!is_numeric($plan_id)) {
			return $this->buildResponse('failed', 500, 'Failed to get plan_id after create plan.');
		}

		try {
			$plan_result['data']['id'] = $plan_result['data']['plan_id'];
			$scom_community = $this->loadCom('community', $plan_result['data']);

			$comm_result = $scom_community->createDefaultCommunity();
			//\GtoolsDebug::testLog(__METHOD__,[$scom_community, $comm_result]);
			if (201 == $comm_result['code']) {
				$scom_community->createForumGroup($comm_result['data'], $plan_result['data']['creator']);
			}
		} catch (Exception $e) {
			\Yii::info($e->getMessage(), __METHOD__);
		}
		//\GtoolsDebug::testLog(__METHOD__, [$comm_result]);
		return $plan_result;
	}

	/**
	 * 添加/更新计划详情
	 * @param array $plan
	 * @return array|bool
	 */
	public function savePlan(array $plan) {
		if (isset($plan['plan_id']) && !empty($plan['plan_id'])) {
			$model_plan = ModPlan::findOne($plan['plan_id']);
			$model_plan->setAttribute('update_time', date('Y-m-d H:i:s', time()));
			unset($plan['creator']);
			$model_plan->setAttributes($plan);
		} else {
			$model_plan = new ModPlan();
			$model_plan->setAttributes($plan);
			$model_plan->setAttribute('plan_ct', date('Y-m-d H:i:s', time()));
			$model_plan->setAttribute('plan_status', ModPlan::STATUS_NORMAL);
		}

		$result = $model_plan->save();
		if (true === $result) {
			$result = $model_plan->getAttributes();
		}

		if (false === $result || empty($result)) {
			return $this->buildResponse('error', 400, $model_plan->getErrors());
		}

		return $this->buildResponse('success', 201, $result);
	}

	/**
	 * （软）删除计划
	 * @param $plan_id
	 * @return bool
	 */
	public function setPlanStatus($plan_id, $status) {
		if (($plan_o = ModPlan::findOne($plan_id)) != null) {
			$plan_o->setAttribute('plan_status', $status);
			return $plan_o->save();
		} else {
			return false;
		}
	}

	/**
	 * @param $plan_id
	 * @param array $role
	 * @param array $status
	 * @return array|bool
	 */
	public function getParticipants($plan_id, $role = [], $status = [ModPlan::STATUS_NORMAL, ModPlan::STATUS_PUBLISHED], $offset = 0, $limit = null) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			$map = [];
			$map['status'] = $status;
			!empty($role) && $map['role'] = $role;
			$participants = Participant::getParticipants($plan_id, $map, $offset, $limit);
			return $participants;
		} else {
			return $this->buildResponse('error', 400, 'plan was not found');
		}
	}

	public function getStudentsParticipants($map, $per_page = 10, $page = 1) {
		$lists = Participant::find()->where($map)->groupBy('unique_code')->offset($per_page * ($page - 1))->limit($per_page)->asArray()->all();
		$count = Participant::find()->where($map)->groupBy('unique_code')->count();

		return [$lists, $count];
	}

	/**
	 * 获取unique_code用户所在的所有计划
	 * @param $unique_code
	 * @param array $map
	 * @param int $offset
	 * @param int $limit
	 * @return array|\yii\db\ActiveRecord[]
	 */
	public function getStudentsPlans($unique_code = '', $map, $per_page = 10, $page = 1) {
		$query = ModPlan::find()
			->from(['p' => ModPlan::tableName()])
			->join('INNER JOIN', Participant::tableName() . ' AS m', 'p.plan_id = m.plan_id')
			->where($map);

		$query->andWhere(['unique_code' => $unique_code]);

		if (-1 != ($per_page * ($page - 1))) {
			$query->offset($per_page * ($page - 1));
		}

		if (-1 != $per_page) {
			$query->limit($per_page);
		}

		return [$query->asArray()->all(), $query->count()];
	}

	public function getParticipant($plan_id, $participant_id) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			return Participant::getParticipant($participant_id)->getAttributes();
		} else {
			return $this->buildResponse('error', 400, 'plan was not found');
		}
	}

	public function getParticipantByUniqueCode($plan_id, $unique_code) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			return Participant::getMember(['plan_id' => $plan_id, 'unique_code' => $unique_code])->getAttributes();
		} else {
			return $this->buildResponse('error', 400, 'plan was not found');
		}
	}

	public function deleteParticipant($plan_id, $participant_id) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			return Participant::updateAll(['status' => Participant::STATUS_DELETED], ['participant_id' => $participant_id, 'plan_id' => $plan_id]);
		} else {
			return $this->buildResponse('error', 400, 'plan was not found');
		}
	}

	public function saveParticipants($plan_id, $participants_data) {
		if (($plan = ModPlan::findOne($plan_id))) {
			$members = [];
			foreach ($participants_data as $item) {
				if (!empty($participant = Participant::find()->where(['unique_code' => $item['unique_code'], 'plan_id' => $plan_id, 'role' => $item['role']])->one())) {
				} else {
					$participant = new Participant();
					$participant->plan_id = $plan->plan_id;
					$participant->confirmed = Participant::UNCONFIRMED;
				}
				$participant->status = Participant::STATUS_NORMAL;
				$participant->setAttributes($item);

				if (false === $participant->save()) {
					return $this->buildResponse('failed', 400, $participant->getFirstErrors());
				}

				$members[] = $participant->getAttributes();
			}
			return $members;
		} else {
			return $this->buildResponse('error', 400, 'plan was not found');
		}
	}

	public function editParticipant($id, $participants_data) {
		if (($participant = Participant::findOne($id))) {

			$participant->setAttributes($participants_data);

			if (false === $participant->save()) {
				return $this->buildResponse('failed', 400, 'failed to add Participant resource');
			}

			return $participant->getAttributes();
		} else {
			return $this->buildResponse('error', 400, 'participant was not found');
		}
	}

	public function getGroups($plan_id) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			return TraineeGroup::getPlanGroups($plan_id);
		} else {
			return false;
		}
	}

	public function getGroup($plan_id, $group_id) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			return TraineeGroup::getGroupById($group_id);
		} else {
			return false;
		}
	}

	public function deleteGroup($plan_id, $group_id) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			return $plan->deleteGroup($group_id);
		} else {
			return false;
		}
	}

	public function saveGroup($plan_id, $group_data) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			return $plan->saveGroup($group_data);
		} else {
			return false;
		}
	}

	public function getGroupMembers($plan_id, $group_id) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			$group = TraineeGroup::getGroupById($group_id);
			if (!empty($group)) {
				return [$group->getMembers(), $group->countMembers()];
			}

			return $this->buildResponse('error', 400, 'group was not found');
		} else {
			return $this->buildResponse('error', 400, 'plan was not found');
		}
	}

	/**
	 * 获取可添加到当前小组的学员
	 * @param $plan_id 计划ID
	 * @param int|array $group_id 当前小组的ID，为0表示新的小组
	 */
	public function getGroupAvailableTrainees($plan_id, $group_id) {
		$groups = $this->getGroups($plan_id);

		$groups = $groups['group'];

//        \GtoolsDebug::testLog(__METHOD__, $groups, __FILE__ . __LINE__);

		$groupParticipants = RsTraineeGroup::getItems(['group_id' => array_column($groups, 'group_id')]);

		if (!is_array($group_id)) {
			$group_id = [$group_id];
		}

//        \GtoolsDebug::testLog(__METHOD__, $group_id, __FILE__ . __LINE__);

		$unGroupids = array_diff(array_column($groups, 'group_id'), $group_id);

		$availableCheckedParticipant = [];
		$availableUncheckedParticipant = [];
		$unavailable = [];
		foreach ($groupParticipants as $participant) {
			if (in_array($participant['group_id'], $unGroupids)) {
				$unavailable[] = $participant['participant_id'];
			} else if (in_array($participant['group_id'], $group_id)) {
				$availableCheckedParticipant[] = $participant['participant_id'];
			} else {
				$availableUncheckedParticipant[] = $participant['participant_id'];
			}

		}

		$participants = $this->getParticipants($plan_id, [Participant::TYPE_TRAINEE, Participant::TYPE_MONITOR]);

		$members = [];
		foreach ($participants[0] as $participant) {
			if (in_array($participant['participant_id'], $unavailable)) {
				continue;
			}

			if (in_array($participant['participant_id'], $availableCheckedParticipant)) {
				$participant['checked'] = true;
			} else {
				$participant['checked'] = false;
			}

			$members[] = $participant;
		}

		return $members;
	}

	/**
	 * @param $plan_id
	 * @param $group_name
	 * @param array $participant_id
	 * @param int $group_id
	 * @return \service\base\type
	 * 新增/修改成员分组
	 */
	public function saveTraineeGroup($plan_id, $group_name, array $participant_id, $group_id = 0) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			if (empty($group_id)) {
				//新增
				$group_id = TraineeGroup::addGroup($plan_id, $group_name);
				RsTraineeGroup::addParticipant($group_id, $participant_id);
				return $this->buildResponse('success', 200, $group_id);

			} else {
				//修改
				TraineeGroup::modGroup($group_id, $group_name);

				RsTraineeGroup::deleteAll(['group_id' => $group_id]);
				RsTraineeGroup::addParticipant($group_id, $participant_id);
				return $this->buildResponse('success', 200, $group_id);
			}
		} else {
			return $this->buildResponse('error', 400, 'plan was not found');
		}
	}

	public function getTraineeGroup($plan_id, $group_id) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			return TraineeGroup::getGroup($group_id);
		} else {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

	}

	public function deleteGroupMember($plan_id, $group_id, $participant_id) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			$group = TraineeGroup::getGroupById($group_id);
			return $group->deleteParticipant($participant_id);
		} else {
			return $this->buildResponse('error', 400, 'plan was not found');
		}
	}

	/**
	 * @deprecated
	 * @param $plan_id
	 * @param $schedule_id
	 * @return bool|null|static
	 */
	public function getSchedule($plan_id, $schedule_id) {
		if (($plan = ModPlan::findOne($plan_id))) {
			if (null == ($schedule = Schedule::getScheduleById($schedule_id))) {
				return $this->buildResponse('error', 400, 'schedule was not found');
			}

			return $schedule;
		}
		return $this->buildResponse('error', 400, 'plan was not found');
	}

	/**
	 * @deprecated
	 * @param $plan_id
	 * @param $schedule_id
	 * @return bool
	 */
	public function deleteSchedule($plan_id, $schedule_id) {
		if (($plan = ModPlan::getPlanById($plan_id)) != null) {
			return false === Schedule::deleteSchedule($schedule_id) ? $this->buildResponse('failed', 400, 'failed to delete schedule') : true;
		} else {
			return $this->buildResponse('error', 400, 'plan was not found');
		}
	}

	/**
	 * @deprecated
	 * @param $plan_id
	 * @param array $schedule
	 * @return bool
	 */
	public function saveSchedule($plan_id, $schedule = []) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
			return $plan->saveSchedule($schedule);
		} else {
			return false;
		}
	}

	public function loadSchedule($plan_id) {
		if (($plan = ModPlan::getPlanById($plan_id)) == null) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		$schedule = Base::loadService('service\plan\Schedule', ['entity' => $plan, 'plan' => $this]);

		if (empty($schedule)) {
			return $this->buildResponse('error', 400, 'failed to load schedule service');
		}

		return $schedule;
	}

	public function loadTag($plan_id) {
		if (($plan = ModPlan::findOne($plan_id)) == null) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		$s = Base::loadService('service\plan\Tag', ['plan' => $plan, 'context' => $this]);
		if (empty($s)) {
			return $this->buildResponse('error', 400, 'failed to load tag service');
		}

		return $s;
	}

	public function loadMember($plan_id) {
		if (($plan = ModPlan::findOne($plan_id)) == null) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		$member = Base::loadService('service\plan\Member', ['plan_entity' => $plan, 'plan' => $this]);
		if (empty($member)) {
			return $this->buildResponse('error', 400, 'failed to load member service');
		}

		return $member;
	}

	public function loadResource($plan_id) {
		if (($plan = ModPlan::findOne($plan_id)) == null) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		$resource = Base::loadService('service\resource\Resource', ['entity' => $plan, 'plan' => $this]);
		if (empty($resource)) {
			return $this->buildResponse('error', 400, 'failed to load resource service');
		}

		return $resource;
	}

	/**
	 * @return \service\plan\Survey
	 */
	public function loadSurvey() {
		$survey = Base::loadService('service\plan\Survey', [], $this);
		if (empty($survey)) {
			return $this->buildResponse('error', 400, 'failed to load paln\'s survey service');
		}

		return $survey;
	}

	/**
	 * 获取计划的标签
	 * @param int $plan_id 计划的ID
	 */
	public function getPlanTags($plan_id) {
		if (($plan = ModPlan::findOne($plan_id)) == null) {
			return false;
		}

		return $plan->getTags();
	}

	/**
	 * 为计划添加标签
	 * @param int $plan_id
	 * @param array $tags 标签组成的数组 [[tag_id:3,tag_name:'tag1'],[tag_id:4,tag_name:'tag2'],[tag_name:'tag3']]
	 * @return bool
	 */
	public function addPlanTags($plan_id, array $tags) {
		if (($plan = ModPlan::findOne($plan_id)) == null) {
			return false;
		}

		return $plan->addTags($tags);
	}

	/**
	 * 删除计划的标签
	 * @param int $plan_id 计划的ID
	 * @param array $tag_ids 待删除的标签的ID组成的数组
	 */
	public function deletePlanTags($plan_id, array $tag_ids = []) {
		if (($plan = ModPlan::findOne($plan_id)) == null) {
			return false;
		}

		return $plan->deleteTags($tag_ids);
	}

	/**
	 * @param $plan_id
	 * @param participant_id
	 * @return bool
	 */
	public function addMonitors($plan_id, $participant_id) {
		if (($plan = ModPlan::findOne($plan_id)) == null) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		return Participant::setMonitors(['plan_id' => $plan_id, 'participant_id' => $participant_id, 'role' => Participant::TYPE_TRAINEE]);
	}

	/**
	 * @param $plan_id
	 * @param array $unique_code 用户unique_code组成的数组 (['xxxxxxx','cccccccccc','zzzzzzzzzzzz']) 为空时表示取消全部班长角色
	 * @return bool
	 */

	public function removeMonitors($plan_id, $participant_id) {
		if (($plan = ModPlan::findOne($plan_id)) == null) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		$where = ['plan_id' => $plan_id];
		if (!empty($participant_id)) {
			$where['participant_id'] = $participant_id;
		}

		return Participant::removeMonitors($where);
	}

	/**
	 * @param $plan_id
	 * @param array $unique_code 用户unique_code组成的数组 (['xxxxxxx','cccccccccc','zzzzzzzzzzzz']) 为空时表示获取所有班长的权限设置
	 * @return array|bool
	 */
	public function getMonitorsAuth($plan_id, array $unique_code = []) {
		if (($plan = ModPlan::findOne($plan_id)) == null) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		$where = ['plan_id' => $plan_id, 'role' => Participant::TYPE_TRAINEE];
		if (!empty($unique_code)) {
			$where['unique_code'] = $unique_code;
		}

		return Participant::getMonitors($where);
	}

	/**
	 * @param $plan_id
	 * @param array $monitors [
	 * [ unique_code: 'cab1c9dc2d3f928518b654554c2320fbf9fa07d0d0a677e26204dfee6165ff7c',
	 * auth: [ view_sign: 1, view_survey: 0, view_examination: 1, view_download_file: 0 ] ]
	 * ]
	 */
	public function setMonitorsAuth($plan_id, array $monitors = []) {
//        if (($plan = ModPlan::findOne($plan_id)) == null)
		//            return $this->buildResponse('error', 400, 'plan was not found');
		//        return Participant::setMonitorsAuth($plan_id, $monitors);
	}

	/**
	 * @param $plan_id
	 * @param $unique_code
	 * @param array $auth [ view_sign: 1, view_survey: 0, view_examination: 1, view_download_file: 0 ]
	 * @return \service\base\type
	 */
	public function setMonitorAuth($plan_id, $unique_code, $auth = []) {
		if (($plan = ModPlan::findOne($plan_id)) == null) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		$train = Participant::getMember(['plan_id' => $plan_id, 'unique_code' => $unique_code, 'role' => Participant::TYPE_TRAINEE]);
		if (!empty($train) && $train->isMonitor()) {
			return $train->setMonitorAuth($auth)->save();
		}

		return $this->buildResponse('failed', 400, 'failed to set monitor\'s auth');
	}

	/**
	 * 获取所有该计划下的资料
	 * @deprecated
	 * @param $plan_ids
	 * @return array|\service\base\type|\yii\db\ActiveRecord[]
	 */
	public function getResources($plan_ids) {
		$where = [];
		!empty($plan_ids) && $where['plan_id'] = $plan_ids; //return $where;
		$resources = RsPlanResource::find()->where($where)->asArray()->all();
		if (false === $resources) {
			return $this->buildResponse('failed', 400, 'failed to get plan resources');
		}

		empty($resources) && $resources = [];
		return $resources;
	}
	/**
	 * 获取所有该计划下的资料的用户
	 * @deprecated
	 * @param $plan_ids
	 * @return array|\service\base\type|\yii\db\ActiveRecord[]
	 */
	public function getResourcesUsers($plan_ids, $resource_ids) {
		$wh['plan_id'] = $plan_ids;
		$wh['resource_id'] = $resource_ids;
		$where = [];
		if (!empty($plan_ids)) {
			$where = $wh;
		} //return $where;
		$resources = RsPlanResourceUsers::find()->where($where)->asArray()->all();
		if (false === $resources) {
			return $this->buildResponse('failed', 400, 'failed to get plan resources_users');
		}

		empty($resources) && $resources = [];
		return $resources;
	}

	/**
	 * @param $plan_id
	 * @param string $tag_filter
	 * @param int $offset
	 * @param int $limit
	 * @return \service\base\type
	 */
	public function getResources_v2($plan_id, $tag_filter = '', $offset = 0, $limit = 20) {
		$resource_ids = RsPlanResource::getPlanResourceIds($plan_id);

		$all_resource_ids = RsPlanResource::getPlanResourceIds($plan_id);

		if (false === $resource_ids) {
			return $this->buildResponse('failed', 400, 'failed to get plan resources');
		}

		empty($resource_ids) && $resource_ids = [];
		empty($all_resource_ids) && $all_resource_ids = [];

		$rService = Base::loadService('service\resource\Resource');

		$all = $rService->getResources(['resource_id' => $all_resource_ids], $tag_filter);
		$resources = $rService->getResources(['resource_id' => $resource_ids], $tag_filter);

		return [array_slice($resources, $offset, $limit), count($all)];
	}

	/**
	 * @param $plan_id
	 * @param $resource_id
	 * @return array|\service\base\type
	 */
	public function addResource($plan_id, $resource_id) {
		$model = new RsPlanResource();
		$model->setAttributes(['plan_id' => $plan_id, 'resource_id' => $resource_id]);
		if (false === $model->save()) {
			return $this->buildResponse('failed', 400, 'failed to add plan resource');
		}

		return $model->getAttributes();
	}

	/**
	 * @param $plan_id
	 * @param array $resource_ids
	 * @return bool|\service\base\type
	 */
	public function removeResources($plan_id, $resource_ids) {
		if (false == ($rs = RsPlanResource::deleteAll(['plan_id' => $plan_id, 'resource_id' => $resource_ids]))) {
			return $this->buildResponse('failed', 400, 'failed to remove resource');
		}
		return true;
	}

	/**
	 * 确认参与计划
	 * @param $plan_id
	 * @param $participant_id
	 * @return bool|\service\base\type
	 */
	public function confirmPlan($plan_id, $participant_id) {
		$plan = ModPlan::getPlanById($plan_id);

		if (empty($plan)) {
			return $this->buildResponse('failed', 400, 'plan was not found');
		}

		$participant = Participant::getParticipant($participant_id);

		if (empty($participant)) {
			return $this->buildResponse('failed', 400, 'participant was not found');
		}

		if (false === $participant->confirm()) {
			return $this->buildResponse('failed', 400, $participant->firstErrors);
		} else {
			return true;
		}

	}

	public function load($plan_id) {
		$plan_entity_result = $this->getPlan($plan_id);
		if (array_key_exists('plan_id', $plan_entity_result)) {
			return $this->buildResponse('success', 201, $plan_entity_result);
		}

		return $plan_entity_result;
	}

	/**
	 * Note: Internal invoke.
	 *
	 * @param type $entity_id
	 */
	public function loadBaseEntity($entity_id) {
		$entity_model = ModPlan::getPlanById($entity_id, true);
		if (empty($entity_model)) {
			return $this->buildResponse('error', 400, 'Invalid entity_id.');
		}

		$entity_model['id'] = $entity_model['plan_id'];
		return $this->buildResponse('success', 201, $entity_model);
	}

	public function loadDiscussion() {
		return Base::loadService('service\plan\Discussion', [], $this);
	}

	/**
	 * 获取计划关联的微信应用帐号信息
	 * @param $plan_id
	 * @return array|\service\base\type|\yii\db\ActiveRecord[]
	 */
	public function getApplication($plan_id) {
		$plan = ModPlan::getPlanById($plan_id, true);

		if (empty($plan)) {
			return $this->buildResponse('error', 400, 'plan was not found');
		}

		$where = [];
		$where['id'] = $plan['application_id'];
		$ret = ModApplication::find()->where($where)->asArray()->one();
		if (false === $ret) {
			return $this->buildResponse('failed', 400, 'failed to get plan Application');
		}

		empty($ret) && $ret = [];
		return $ret;
	}

	public function getPlanManager($plan_id = 0) {
		$plan = ModPlan::findOne($plan_id)->toArray();

		$creator_unique = $plan['creator'];

		$user = new \service\user\User();
		$creator = $user->loadByCode($creator_unique);

		$data = [];
		$data['plan_id'] = $plan_id;
		$data['role'] = 'PlanManager';
		$data['name'] = $creator['data']['user_name'];
		$data['unique_code'] = $creator_unique;

		if (false == ($superMan = Participant::find()->where($data)->one())) {
			$superMan = new Participant();
			$superMan->setAttributes($data);
			$superMan->save();
		}

		$where[] = 'AND';
		$where[] = ['plan_id' => $plan_id];
		$where[] = ['role' => 'PlanManager'];
		$where[]=['status'=>1];
		$where[] = ['<>', 'unique_code', $creator_unique];
		$managers = Participant::find()->where($where)->all();

		$where = [];
		$where[] = 'AND';
		$where[] = ['plan_id' => $plan_id];
		$where[] = ['<>', 'role', 'PlanManager'];
		$where[]=['status'=>1];
		$where[] = ['<>', 'unique_code', $creator_unique];
		foreach ($managers as $value) {
			$where[] = ['<>', "unique_code", $value['unique_code']];
		};

		$person = Participant::find()->where($where)->orderBy('participant_id desc')->all();

		return ['creator' => $superMan, 'managers' => $managers, 'person' => $person];
	}

	public function setEnrollSetting($planId, array $setting = []) {
		$setting['plan_id'] = $planId;
		$enroll_o = ModEnroll::findOne($data['id']);
		$enroll_o->setAttributes($data);
		if (true === $enroll_o->save()) {
			return true;
		}

		return false;
	}

	public function courseCheck($planId, $participantId) {
		// TODO: Implement courseCheck() method.
	}

	public function listCourseWorks($planId) {
		// TODO: Implement listCourseWorks() method.
	}

	/** 根据uniqueCode获取用户参与信息
	 * @param $plan_id
	 * @param $unique_code
	 * @param array $where
	 * @return array
	 */
	public function getParticipantByUser($plan_id, $unique_code, array $where = []) {
		if (($plan = ModPlan::findOne($plan_id)) != null) {
//            $where = ArrayHelper::merge($where, ['plan_id' => $plan_id, 'unique_code' => $unique_code]);
			$participant = Participant::getMember($where);
			if (is_null($participant)) {
				return $this->buildResponse('error', 400, 'participant not found');
			}

			return $participant->getAttributes();
		} else {
			return $this->buildResponse('error', 400, 'plan was not found');
		}
	}

}
