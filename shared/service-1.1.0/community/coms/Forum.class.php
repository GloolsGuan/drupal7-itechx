<?php
namespace service\community\coms;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class Forum extends \service\base\Component
{

    public function __construct($id = '', $parent = null, $config = array())
    {
        parent::__construct($id, $parent, $config);
    }

    public function init()
    {
        //\GtoolsDebug::testLog(__METHOD__, [$this->base_entity, $this->base_entity_id]);
        if (empty($this->base_entity) || !is_numeric($this->base_entity['id'])) {
            if (is_numeric($this->base_entity_id)) {
                //\GtoolsDebug::testLog(__METHOD__, $this->module);
                $base_entity_result = $this->module->loadBaseEntity($this->base_entity_id);
                if (201 !== $base_entity_result['code']) {
                    throw new \Exception('System error: Failed to load service component \service\community\coms\Forum, Parameter "base_entity" is invalid.', 500);
                }
                $this->base_entity = $base_entity_result['data'];
            }
        }
    }

    protected function execStatistics($type = '')
    {
        if (empty($type) || !is_string($type) || 'normal' !== $type) {
            return $this->buildResponse('error', 400, 'Invalid request for executing statistics of fourm component.');
        }
        $model_forum = new \service\community\models\Topic();
        //$db = $model_forum->getDb();
        //\GtoolsDebug::testLog(__METHOD__, $query);
        $count_topics = $model_forum->find()->select(['count(id) as total_count', 'group_id'])->where(['parent_id' => 0])->groupBy('group_id')->asArray()->indexBy('group_id')->all();
        $count_all = $model_forum->find()->select(['count(id) as total_count', 'group_id'])->groupBy('group_id')->asArray()->indexBy('group_id')->all();

        //$count_topics = $db->createCommand('select count(id) from community_topic where parent_id=0 group by group_id;')->execute();
        $statistics = [];
        foreach ($count_topics as $group_id => $attrs) {
            $total_comments = $count_all[$group_id]['total_count'] - $attrs['total_count'];
            $statistics[$group_id] = [
                'group_id' => $group_id,
                'topics' => $attrs['total_count'],
                'comments' => $total_comments,
                'grade' => $attrs['total_count'] + ceil($total_comments / 2)
            ];
        }
        //\GtoolsDebug::testLog(__METHOD__, $statistics);
        return $statistics;
    }

    public function loadStatistics($group_id, $type = 'normal', $version = 0)
    {
        $group_statistics = $this->execStatistics($type);

        if (array_key_exists($group_id, $group_statistics)) {
            return $this->buildResponse('success', 201, $group_statistics[$group_id]);
        }


    }

    public function postTopic($topic_entity, $group_id, $member_id, $category_id = 0, $tags = '')
    {
        //\GtoolsDebug::testLog(__METHOD__, [$topic_entity, $group_id]);
        if (!is_numeric($group_id)) {
            return $this->buildResponse('error', 400, sprintf('Client error: invalid parameter group_id, The value must be an numeric, "%s" provided..', $group_id));
        }
        $model_forum = new \service\community\models\Topic();
        $topic_entity['group_id'] = $group_id;

        if (is_numeric($member_id)) {
            $topic_entity['member_id'] = $member_id;
        } else {
            //-- Short time --
            $topic_entity['member_uc_code'] = $member_id;
        }
        $model_forum->setAttributes($topic_entity)->validate();
        if ($model_forum->hasErrors()) {
            \GtoolsDebug::testLog(__METHOD__, $model_forum->getErrors());
            $this->buildResponse('error', 401, $model_forum->getErrors());
        }

        $forum_entity = $model_forum->insert();
        //\GtoolsDebug::testLog(__METHOD__, [$forum_entity, $model_forum->getErrors('db.insert')]);
        return $this->buildResponse('success', 201, $forum_entity);
    }


    public function loadTopic($topic_id)
    {

        if (!is_numeric($topic_id)) {
            return $this->buildResponse('success', 401, 'Invalid parameter for loadding topic');
        }

        $model_topic = new \service\community\models\Topic();
        $topic_entity = $model_topic->find()->where(['id' => $topic_id])->asArray()->one();
        if (empty($topic_entity)) {
            return $this->buildResponse('success', 299, null);
        }

        return $this->buildResponse('success', 201, $topic_entity);
    }

    
    public function loadLatestTopics($group_id, $member_id = null, $parent_id = 0, $updated_at = null, $last_topic_id = null, $rows = 20, $offset = 0)
    {
        $model_topic = new \service\community\models\Topic();
        
        if (!is_numeric($group_id) || !is_numeric($parent_id)) {
            return $this->buildResponse('error', 401, 'group_id  and parent_id are required, and They must be numeric.');
        }

        if (is_numeric($member_id)) {
            $query = $model_topic->find()->where(['group_id' => $group_id, 'parent_id' => $parent_id, 'status' => 'active', 'member_id' => $member_id]);
        } else {
            $query = $model_topic->find()->where(['group_id' => $group_id, 'parent_id' => $parent_id, 'status' => 'active']);
        }

        if (is_numeric($updated_at) && $updated_at < time() && is_numeric($last_topic_id)) {
            $query->andWhere('updated_at>=:updated_at', [':updated_at' => $updated_at]);
            $query->andWhere('id>:last_topic_id', [':last_topic_id' => $last_topic_id]);
        }

        $count = $query->count();

        $query->orderBy('created_at desc, updated_at desc');
        $query->offset($offset);
        $query->limit($rows);


        $yii_query_builder = new \yii\db\QueryBuilder($model_topic->getDb());
        //\GtoolsDebug::testLog(__METHOD__, $yii_query_builder->build($query));

        $topics = $query->asArray()->all();
        
        $result[0] = $topics;
        $result[1] = $count;

        if (!empty($topics)) {
            return $this->buildResponse('success', 201, $result);
        }
        return $this->buildResponse('success', 200, []);
    }
    

    public function loadForumList()
    {

    }


    public function loadTopicsFromGroup($page, $rows_per_page = 50, $group_id)
    {
        $rows = $rows_per_page;
    }


    public function loadTopicsForMeFromGroup($page, $page_rows = 50, $group_id, $member_id)
    {

    }


    public function updateTopic($topic_id, $topic_entity)
    {

    }


    public function removeTopic($topic_id, $operator_code)
    {

    }

    public function loadCategories($group_id)
    {

    }

    public function createCategory($category_entity, $group_id, $category_parent_id)
    {

    }

    public function updateCategory($category_id, $category_entity)
    {

    }

    public function removeCategory()
    {

    }

    public function createComment($comment_entity, $parent_id)
    {

    }


    public function updateComment($comment_id, $comment_entity)
    {

    }


    public function removeComment($comment_id, $operator_code)
    {

    }


    protected function _createTopic($topic_entity, $parent_id = 0)
    {

    }


    protected function _removeTopic($topic_id, $operator_code)
    {

    }


    protected function _updateTopic($topic_id, $entity, $operator_code)
    {

    }
}