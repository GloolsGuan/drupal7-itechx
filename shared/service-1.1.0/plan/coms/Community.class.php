<?php
namespace service\plan\coms;
    /*
     * 社区服务以组件的方式对计划提供支持，依赖核心社区模块\service\community
     *
     * 一个计划默认会配套一个独立的计划事务社区，也可以理解为计划协作团队。
     * 一个默认的公共用户组（group）,原则上所有的计划成员都在这个公共的组里。
     *
     * 在规划中，service\plan\community 默认提供了以下服务项目：
     *   - 公共用户组，社区组件会默认为计划创建公共用户组，有四个状态可用，同时提供settings
     *     属性可独立设置用户参数，但是这些参数不参数搜索。
     *   - 论坛组，默认提供。
     *   - 自定义用户组，可自行创建用户组，并设置该用户组所启用的服务组件，例如论坛、活动等。
     *   -
     */

/**
 * Description of Schedule
 *
 * @author glools
 */
class Community extends \service\base\Component
{
    //put your code here

    protected $base_entity = null;

    protected $base_entity_id = null;

    protected $comm = null;


    public function init()
    {
        if (empty($this->base_entity) || !is_numeric($this->base_entity['id'])) {
            if (is_numeric($this->base_entity_id)) {
                $base_entity_result = $this->module->loadBaseEntity($this->base_entity_id);
                //\GtoolsDebug::testLog(__METHOD__, [$this->base_entity_id, $this->base_entity]);
                if (201 !== $base_entity_result['code']) {
                    throw new \Exception('System error: Invalid plan\coms\forum component api request, base_entity_id is required.', 500);
                }
                $this->base_entity = $base_entity_result['data'];
            }

        }

        $this->base_entity_id = $this->base_entity['id'];
        $this->comm = new \service\community\Community();
    }


    public function createDefaultCommunity()
    {
        $comm_entity = [
            'title' => sprintf('%s 默认群', $this->base_entity['plan_name']),
            'master_title' => $this->buildMasterTitle(),
            'master_code' => $this->buildMasterCode(),
            'master_ext_id' => $this->base_entity['id'],
            'founder_uc_code' => $this->base_entity['creator']
        ];


        if (empty($this->comm)) {
            return $this->buildResponse('failed', 501, 'Failed to create default community for plan, The community service is not exists.');
        }


        $result = $this->comm->build($comm_entity, $this->base_entity['creator']);

        return $result;
    }


    public function loadDefaultCommunity()
    {
        static $community = null;
        if (!empty($community)) {
            return $this->buildResponse('success', 201, $community);
        }
        //\GtoolsDebug::testLog(__METHOD__, $this->base_entity);
        $community_result = $this->comm->loadByMasterExtId($this->buildMasterCode(), $this->base_entity['id']);
        //\GtoolsDebug::testLog(__METHOD__, $community_result);
        if (201 !== $community_result['code']) {
            \Yii::error(sprintf('System error: There is no default community for current plan with id %s and title %s.', $this->base_entity['id'], $this->base_entity['plan_name']), __METHOD__);
            return $this->buildResponse('success', 299, null);
        }
        $community = $community_result['data'];

        return $this->buildResponse('success', 201, $community);
    }


    protected function buildMasterTitle()
    {
        return get_class($this->module);
    }


    protected function buildMasterCode()
    {
        $master_title = $this->buildMasterTitle();
        return hash('sha256', $master_title);
    }


    public function createForumGroup($community_entity, $creator_uc_code)
    {
        $group_entity_data = [
            'community_id' => $community_entity['id'],
            'title' => 'forum_group',
            'type' => 'forum',
            'creator_uc_code' => $creator_uc_code
        ];

        $scom_group = $this->comm->loadCom('group', $community_entity);
        $group_result = $scom_group->createGroup($group_entity_data, $creator_uc_code);

        return $group_result;
    }


    public function loadForumGroup()
    {
        $community_result = $this->loadDefaultCommunity();
        if (201 !== $community_result['code']) {
            return $community_result;
        }
        $community = $community_result['data'];

        $scom_group = $this->comm->loadCom('group', $community);
        $groups = $scom_group->loadGroupsByType('forum');

        if (201 !== $groups['code']) {
            return $this->buildResponse('failed', 500, 'There is no forum group.');
        }
        //\GtoolsDebug::testLog(__METHOD__, $groups);
        return $this->buildResponse('success', 201, array_pop($groups['data']));
    }


    public function loadGroups()
    {
        $community_result = $this->loadDefaultCommunity();
        if (201 !== $community_result['code']) {
            return $community_result;
        }
        $community = $community_result['data'];

        $scom_group = $this->comm->loadCom('group', $community);
        $groups = $scom_group->loadGroups();

        if (empty($groups)) {
            return $this->buildResponse('success', 200, []);
        }
        $group_statistics = [];
        foreach ($groups as $group) {
            $group_statistics['entity'] = $group;
            $group_statistics['statistics'] = $scom_group->loadStatistic($group['id']);
        }

        return $this->buildResponse('success', 201, $group_statistics);
    }


    public function executeGroupStatistics($group)
    {

    }

    public function postTopic($topic_entity, $group_id, $member_uc_code, $category_id = 0, $tags = '')
    {
        $community_result = $this->loadDefaultCommunity();

        if (201 !== $community_result['code']) {
            return $community_result;
        }
        $community = $community_result['data'];
        $scom_forum = $this->comm->loadCom('forum', $community);

        if (false === $scom_forum) {
            return $this->buildResponse('failed', 500, 'System error, There is no forum component in community service.');
        }

        return $scom_forum->postTopic($topic_entity, $group_id, $member_uc_code, $category_id, $tags);
    }


    public function loadStatistics($group_id, $type = 'normal')
    {
        $community_result = $this->loadDefaultCommunity();

        if (201 !== $community_result['code']) {
            return $community_result;
        }
        $community = $community_result['data'];

        $scom_forum = $this->comm->loadCom('forum', $community);
        $forum_statistics_result = $scom_forum->loadStatistics($group_id, $type);

        return $forum_statistics_result;
    }


    /**
     * 获取帖子的评论数据
     * @param $topic_id
     * @param int $comment_rows
     * @param int $coment_offset
     * @return \service\base\type
     */
    public function loadTopicComment($topic_id, $comment_rows = 20, $coment_offset = 0)
    {
        $community_result = $this->loadDefaultCommunity();

        if (201 !== $community_result['code']) {
            return $community_result;
        }
        $community = $community_result['data'];

        $scom_forum = $this->comm->loadCom('forum', $community);
        $topic_entity_result = $scom_forum->loadTopic($topic_id);
        if (201 != $topic_entity_result['code']) {
            return $topic_entity_result;
        }
        $topic_entity = $topic_entity_result['data'];
        $comments_result = $scom_forum->loadLatestTopics($topic_entity['group_id'], null, $topic_id, null, null, $comment_rows, $coment_offset);

        return $comments_result;
    }


    public function loadTopic($topic_id, $comment_rows = 100)
    {
        $community_result = $this->loadDefaultCommunity();

        if (201 !== $community_result['code']) {
            return $community_result;
        }
        $community = $community_result['data'];

        $scom_forum = $this->comm->loadCom('forum', $community);
        $topic_entity_result = $scom_forum->loadTopic($topic_id);
        if (201 != $topic_entity_result['code']) {
            return $topic_entity_result;
        }

        $topic_entity = $topic_entity_result['data'];
        $comments_result = $scom_forum->loadLatestTopics($topic_entity['group_id'], null, $topic_id, null, null, $comment_rows);

        if (201 === $comments_result['code']) {
            $comments = $comments_result['data'];
        } else {
            $comments = [];
        }

        return $this->buildResponse('success', 201, [
            'topic' => $topic_entity,
            'comments' => $comments
        ]);
    }


    public function loadLatestTopics($group_id, $member_id = null, $parent_id = 0, $last_updated_at = null, $last_topic_id = null, $rows = 20, $offset = 0)
    {
        $community_result = $this->loadDefaultCommunity();

        if (201 !== $community_result['code']) {
            return $community_result;
        }
        $community = $community_result['data'];

        $scom_forum = $this->comm->loadCom('forum', $community);

        return $scom_forum->loadLatestTopics($group_id, $member_id, $parent_id, $last_updated_at, $last_topic_id, $rows, $offset);
    }


    public function likeTopic($topic_id, $uc_user_code)
    {
        $community_result = $this->loadDefaultCommunity();

        if (201 !== $community_result['code']) {
            return $community_result;
        }

        $community = $community_result['data'];
        $scom_like = $this->comm->loadCom('like', $community);

        return $scom_like->mark($uc_user_code, $this->getTopicLikeObject(), $topic_id, 'collecting');
    }


    public function praiseTopic($topic_id, $uc_user_code)
    {
        $community_result = $this->loadDefaultCommunity();

        if (201 !== $community_result['code']) {
            return $community_result;
        }

        $community = $community_result['data'];
        $scom_like = $this->comm->loadCom('like', $community);

        return $scom_like->mark($uc_user_code, $this->getTopicLikeObject(), $topic_id, 'thumbs_up');
    }

    public function loadTopicAllLikes($topic_id, $uc_user_code)
    {
        $community_result = $this->loadDefaultCommunity();

        if (201 !== $community_result['code']) {
            return $community_result;
        }

        $community = $community_result['data'];
        $scom_like = $this->comm->loadCom('like', $community);

        return $scom_like->getAllOnObject($uc_user_code, $this->getTopicLikeObject(), $topic_id);
    }


    protected function getTopicLikeObject()
    {
        return 'forum.topic';
    }
}
