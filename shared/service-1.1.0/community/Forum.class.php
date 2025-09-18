<?php
namespace service\community;
    /*
     * 独立社区/论坛组件
     * 
     * 社区服务以组件的方式对计划提供支持，依赖核心社区模块\service\community
     * 
     * 
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
class Forum extends \service\base\Module
{
    //put your code here

    protected $master_title = null;

    protected $master_code = null;

    protected $master_ext_id = null;

    public $community_title = '';

    public $operator_uc_code = '';

    public $founder_uc_code = '';


    protected $comm = null;
    protected $community_entity = null;


    public function init()
    {

        $this->comm = new Community();
        $community_result = $this->loadCommunity($this->master_code, $this->master_ext_id);
        if (201 == $community_result['code']) {
            $this->community_entity = $community_result['data'];
        } else {
            $result = $this->createCommunity($this->master_code, $this->master_ext_id, $this->master_title, $this->community_title, $this->founder_uc_code, $this->operator_uc_code);
            if (201 === $result['code']) {
                $this->community_entity = $result['data'];
                $result = $this->createForumGroup($this->founder_uc_code);
                //\GtoolsDebug::testLog(__METHOD__, $result);
            }
        }
    }


    public function setMaster_title($value)
    {
        $this->master_title = $value;
        $this->master_code = $this->buildMasterCode($value);
    }


    public function setMaster_ext_id($value)
    {
        if (!is_numeric($value) && (strlen($value) != 64 || strlen($value) != 32)) {
            throw new \Exception(sprintf('Invalid master_ext_id value, The value "%s" of master_ext_id can be numeric, 64 chars string or 32 chars string.', $value));
        }
        $this->master_ext_id = $value;
    }


    public function createCommunity($master_code, $master_ext_id, $master_title, $community_title, $founder_uc_code, $operator_uc_code)
    {
        $comm_entity = [
            'title' => sprintf('%s 默认群', $community_title),
            'master_title' => $master_title,
            'master_code' => $this->buildMasterCode($master_title),
            'master_ext_id' => $master_ext_id,
            'founder_uc_code' => $founder_uc_code
        ];

        if (empty($this->comm)) {
            return $this->buildResponse('failed', 501, 'Failed to create default community for plan, The community service is not exists.');
        }

        $result = $this->comm->build($comm_entity, $operator_uc_code);

        return $result;
    }


    public function loadCommunity($master_code = null, $master_ext_id = 0)
    {
        if (!empty($this->community_entity)) {
            return $this->buildResponse('success', 201, $this->community_entity);
        }
        //\GtoolsDebug::testLog(__METHOD__, $this->base_entity);
        $community_result = $this->comm->loadByMasterExtId($master_code, $master_ext_id);
        //\GtoolsDebug::testLog(__METHOD__, $community_result);
        if (201 !== $community_result['code']) {
            \Yii::error(sprintf('System error: There is no default community for master "%s".', $this->master_title), __METHOD__);
            return $this->buildResponse('success', 299, null);
        }

        return $this->buildResponse('success', 201, $community_result['data']);
    }


    public function loadDefaultCommunity()
    {
        if (!empty($this->community_entity)) {
            return $this->buildResponse('success', 201, $this->community_entity);
        }

        return $this->buildResponse('success', 299, []);
    }


    protected function buildMasterTitle($master_title)
    {
        $this->master_title = $master_title;
        return $this->master_title;
    }


    protected function buildMasterCode($master_title)
    {
        return hash('sha256', $master_title);
    }


    public function createForumGroup($creator_uc_code)
    {
        $group_entity_data = [
            'community_id' => $this->community_entity['id'],
            'title' => 'forum_group',
            'type' => 'forum',
            'creator_uc_code' => $creator_uc_code
        ];

        $scom_group = $this->comm->loadCom('group', $this->community_entity);
        $group_result = $scom_group->createGroup($group_entity_data, $creator_uc_code);
        //\GtoolsDebug::testLog(__METHOD__, $group_result);
        return $group_result;
    }


    public function loadForumGroup($gorup_id = null)
    {
        if (empty($this->community_entity)) {
            return $this->buildResponse('error', '401', 'Community entity is not exists.');
        }

        $scom_group = $this->comm->loadCom('group', $this->community_entity);
        if (!empty($group_id)) {
            $group = $scom_group->loadGroup($group_id);
        } else {
            $groups = $scom_group->loadGroupsByType('forum');
            //\GtoolsDebug::testLog(__METHOD__, $groups);
            if (201 !== $groups['code']) {
                return $this->buildResponse('failed', 500, 'There is no forum group.');
            }
            $group = array_pop($groups['data']);
        }

        //\GtoolsDebug::testLog(__METHOD__, $group);
        return $this->buildResponse('success', 201, $group);
    }


    public function _loadGroups()
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


    public function loadLatestTopics($group_id, $member_id = null, $parent_id = 0, $last_updated_at = null, $last_topic_id = null, $rows = 50)
    {
        $community_result = $this->loadDefaultCommunity();

        if (201 !== $community_result['code']) {
            return $community_result;
        }
        $community = $community_result['data'];

        $scom_forum = $this->comm->loadCom('forum', $community);
        
        return $scom_forum->loadLatestTopics($group_id, $member_id, $parent_id, $last_updated_at, $last_topic_id, $rows);
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
