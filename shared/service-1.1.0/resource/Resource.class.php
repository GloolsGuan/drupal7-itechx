<?php

namespace service\resource;

use service\base\Module;
use service\resource\interfaces\Resource as ResourceInterface;
use \service\resource\models\Resource as ModResource;
use \service\base\Base;
use service\resource\models\ResourceTag;
use service\resource\models\RsResourceTag;
use yii\helpers\ArrayHelper;
use service\plan\models\Participant;

/**
 * service
 * Class Resource
 * @package service\resource
 */
class Resource extends Module implements ResourceInterface
{
    public $entity = null;

    public function __construct($id, $parent = null, $config = [])
    {
        parent::__construct([]);
    }

    /**
     *
     * @param array $data [
     * 'resource_name' => 'xxx',
     * 'store_type' => 2,
     * 'is_public' => 1,
     * 'is_protected' => 1,
     * 'resource' => ['xx' => 'xx']
     * ]
     * @return array|\service\base\type
     */
    public function addResource(array $data = [])
    {

        if (empty($data) || !isset($data['store_type']) || empty($data['store_type']) || !isset($data['resource']) || empty($data['resource'])) return $this->buildResponse('failed', 400, 'Invalid parameters for addResource');

        if (!in_array($data['store_type'], [coms\File::STORE_TYPE, coms\RemoteFile::STORE_TYPE])) return $this->buildResponse('error', 400, 'Invalid store_type');
        $resource = new ModResource();
        $data['is_private'] = 1;
//        $data['href'] = $data['resource']['href'];

        $resource->setAttributes($data);

        if (false === $resource->save()) return $this->buildResponse('failed', 400, 'failed to save resource');
        $data['resource']['resource_id'] = $resource->resource_id;

//        return $data['resource'];         //href/resource_id

        if ($resource->store_type == coms\File::STORE_TYPE)
            $com = $this->loadCom('File');
        elseif ($resource->store_type == coms\RemoteFile::STORE_TYPE)
            $com = $this->loadCom('RemoteFile');
        else
            return $this->buildResponse('error', 400, 'unrecognizable store_type was provide');
        $error = $comResource = $com->addResource($data['resource']);
        if ($this->isErrorResponse($error)) return $this->buildResponse($error['status'], $error['code'], $error['data']);
        $resource_data = $resource->getAttributes();
        $resource_data['resource'] = $comResource;
        return $resource_data;
    }

    /**
     *
     * @param $resource_id
     * @return array|\service\base\type
     */
    public function getResource($resource_id)
    {
        if (empty($resource_id)) return $this->buildResponse('failed', 400, 'resource_id is needed');
        $resource = ModResource::findOne($resource_id);
        if (empty($resource)) return $this->buildResponse('error', 400, 'resource was not found');
        if ($resource->store_type == coms\File::STORE_TYPE)
            $com = $this->loadCom('File');
        elseif ($resource->store_type == coms\RemoteFile::STORE_TYPE)
            $com = $this->loadCom('RemoteFile');
        else
            return $this->buildResponse('error', 400, 'unrecognizable store_type was provide');
        $error = $comResource = $com->getResource($resource_id);
        if ($this->isErrorResponse($error)) return $this->buildResponse($error['status'], $error['code'], $error['data']);

        $resource_data = $resource->getAttributes();
        $resource_data['resource'] = $comResource;
        return $resource_data;
    }

    /**
     * @deprecated
     * @param array $resource_ids
     * @param array $tag_filter
     * @param bool $strict
     * @return array|mixed
     */
    public function getResourceBundle(array $resource_ids = [], array $tag_filter = [], $strict = false)
    {
        if (empty($resource_ids) && $strict) return [];

        $where = [];
        if (!empty($resource_ids)) $where = ['resource_id' => $resource_ids];

        $res = $this->getResources($where, $tag_filter);
        return $res;
    }

    /**
     * @param array $map
     * @param array $tag_filter
     * @param array $tagid_filter
     * @return array
     */
    public function getResources(array $map = [], $tag_filter = [], $tagid_filter = [])
    {
        if (!is_array($tag_filter)) $tag_filter = [$tag_filter];
        if (!is_array($tagid_filter)) $tagid_filter = [$tagid_filter];

        $resources = ModResource::getResources($map);

        $data = [];
        if (!empty($tag_filter) || !empty($tagid_filter)) {
            foreach ($resources as &$item) {
                $tags = explode(',', $item['tags']);
                $ids = explode(',', $item['tag_ids']);
                if (!array_intersect($tag_filter, $tags) && !array_intersect($tagid_filter, $ids)) continue;
                $item['tag_pairs'] = array_combine($ids, $tags);
                $data [] = $item;
            }
        } else {
            $data = $resources;
        }

        return $data;
    }

    public function loadTag()
    {
        return Base::loadService('service\resource\Tag');
    }

    /**
     *
     * @param array $tags ['AAA','BBB']
     * @param null $resource_id
     * @return array|bool|\service\base\type|\yii\db\ActiveRecord[]
     */
    public function setTags(array $tags = [], $resource_id = null)
    {
        $res = ResourceTag::addTags($tags);
        if (empty($resource_id)) {
            if (empty($this->entity))
                return $this->buildResponse('error', 400, 'resource_id was not found');
            else
                $resource_id = $this->entity['resource_id'];
        }
        $result = RsResourceTag::setTags($res, $resource_id);
        if (false === $result) return $this->buildResponse('failed', 400, 'failed to set resource tags');
        return $res;
    }

    /**
     * @inheritDoc
     */
    public function getResourceById($id)
    {
        if (false === $item = ModResource::getItemById($id))
            return $this->buildResponse('faile', 400, 'failed to get resource');
        return $this->buildResponse('success', 201, $item->getAttributes());
    }


    /**
     * @inheritDoc
     */
    public function getResourceByIds(array $ids)
    {
        if (false === $items = ModResource::getItemsByid($ids))
            return $this->buildResponse('faile', 400, 'failed to get resource');
        return $this->buildResponse('success', 201, $items);
    }


    /**
     * @inheritDoc
     */
    public function deleteResource(array $ids)
    {
        if (false === ModResource::deleteItems([ModResource::primaryKey()[0] => $ids]))
            return $this->buildResponse('failed', 400, 'failed to delete resource(s)');
        return $this->buildResponse('success', 200, $ids);
    }



    /**
     * admin:管理员，presenter:主持人，participant:参与者，follower:关注者
     * 根据角色获取数据
     */
    public function getRole(array $where)
    {
        $map[] = 'AND';
        $map[] = ['status' => Participant::STATUS_NORMAL];
        $map[] = $where;
        return Participant::getMember($map);
    }

    /**
     * 获取计划下用户及用户参与资料
     * @param $plan_id
     * @param $survey_id
     * @return array
     */
    public function getMaterialUsers($plan_id, $material_id)
    {
        $user = \Yii::loadService('user');
        $plan = \Yii::loadService('plan');
        $participants = $plan->getParticipants($plan_id);

        if (isset($participants[0]) && !empty($participants[0])) {
            $user_unique_codes = array_unique(ArrayHelper::getColumn($participants[0], 'unique_code'));
        } else {
            return $participants;
        }

        $userData = $user->loadListByCodes($user_unique_codes);

        /*if (!empty($material_id)){
            $surveyTokensUser = $this->getLimeToken($material_id);

            $userToken = [];
            if (!empty($surveyTokensUser)) {
                $userToken = ArrayHelper::index($surveyTokensUser, 'lastname');
            }
        }*/

        $userInfo = [];
        if (('success' == $userData['status']) && !empty($userData['data'])) {
            $userInfo = ArrayHelper::index($userData['data'], 'unique_code');
        }


        //合并数据
        /*foreach ($userInfo as $key => &$val) {
            if (isset($userToken[$key])) {
                $val['survey_token'] = $userToken[$key];
            } else {
                $val['survey_token'] = [];
            }
        }*/

        return $userInfo;
    }

}