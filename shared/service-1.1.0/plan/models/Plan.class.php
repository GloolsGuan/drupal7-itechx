<?php
namespace service\plan\models;

use service\base\Base;
use service\base\Error;
use service\plan\models\RsPlanTag as RsPlanTag;
use service\plan\models\Enroll as ModEnroll;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\base\Model;
/**
 * Class Plan
 *
 * @package service\plan\models
 */
class Plan extends \service\base\db\ARecord
{
    //put your code here

    /**
     * 状态：-1：删除；1：禁用，2：正常
     */
    const STATUS_DELETED = -1;
    const STATUS_NORMAL = 1;
    const STATUS_PUBLISHED = 2;

    public function init(){
        
        $this->on(Model::EVENT_BEFORE_VALIDATE, [$this, 'onBeforeValidate']);
        $this->on(static::EVENT_AFTER_UPDATE, [$this, 'onAfterUpdate']);
    }
    

    public static function tableName(){
        return '{{%plan}}';
    }
    

    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'plan_ct',
                'updatedAtAttribute' => 'update_time',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    
    public function onBeforeValidate($event){
        if (substr_count($this->plan_bt, ':')>1) {
            $this->plan_bt  = substr($this->plan_bt, 0, strrpos($this->plan_bt, ':'));
        }
        
        if (substr_count($this->plan_et, ':')>1) {
            $this->plan_et  = substr($this->plan_et, 0, strrpos($this->plan_et, ':'));
        }
        //\GtoolsDebug::testLog(__METHOD__, [$event->sender->plan_bt, $event->sender->plan_et, $this->plan_bt, $this->plan_et]);
    }
    
    public function onAfterUpdate($event){
        //\GtoolsDebug::testLog(__METHOD__, $event->changedAttributes);
        if (substr_count($this->plan_bt, ':')>1) {
            $this->plan_bt  = substr($this->plan_bt, 0, strrpos($this->plan_bt, ':'));
        }
        
        if (substr_count($this->plan_et, ':')>1) {
            $this->plan_et  = substr($this->plan_et, 0, strrpos($this->plan_et, ':'));
        }
    }
    
    
    

    public function rules()
    {
        return [//顺序对规则有影响
            [['plan_name', 'creator', 'plan_addr'], 'trim'],//过滤首尾空格

            [['plan_name', 'creator', 'plan_bt', 'plan_et'], 'required'],//规则字段必须有值

            [['creator'], 'string', 'length' => [64, 64]], //字符串长度必须为64
            ['plan_ct', 'default', 'value' => new Expression('NOW()')],//默认值使用数据库的方法生成
            [['plan_bt', 'plan_et'], 'date', 'format' => 'yyyy-MM-dd HH:mm'],//必须为日期格式
            [['plan_status'], 'default', 'value' => self::STATUS_NORMAL],
            ['plan_et', 'compare', 'compareAttribute' => 'plan_bt', 'operator' => '>'],//将plan_et的值和plan_bt进行对比，plan_et要大于（晚于）plan_bt
        ];
    }


    /**
     * @param array $map
     * @return array
     */
    public static function getPlans(array $map = [], $offset = 0, $limit = 20)
    {
        $_sort = '';
        if (array_key_exists('_sort', $map)) {
            $_sort = $map['_sort'];
            unset($map['_sort']);
        }

        $query = static::find()->from(['p' => static::tableName()])
            ->select([
                'p.*',
                'cash_price' => '(CASE WHEN ISNULL(enroll.cash_price) THEN 0 ELSE enroll.cash_price END)',
                'emcee_total' => '(CASE WHEN ISNULL(emcee.emcee_total) THEN 0 ELSE emcee.emcee_total END)',
                'lectuer_total' => '(CASE WHEN ISNULL(lectuer.lectuer_total) THEN 0 ELSE lectuer.lectuer_total END)',
                'member_total' => '(CASE WHEN ISNULL(mem.member_total) THEN 0 ELSE mem.member_total END)',
                'member_confirmed' => '(CASE WHEN ISNULL(mem.member_confirmed) THEN 0 ELSE mem.member_confirmed END)',
                'schedule_total' => '(CASE WHEN ISNULL(sche.sche_total) THEN 0 ELSE sche.sche_total END)',
                'schedule_deprecated' => '(CASE WHEN ISNULL(sche.sche_deprecated) THEN 0 ELSE sche.sche_deprecated END)',
            ])
            ->where($map)
            ->join('LEFT JOIN', '(SELECT COUNT(*) AS emcee_total,plan_id FROM ' . Participant::tableName() . ' WHERE role =\'' . Participant::TYPE_EMCEE . '\' GROUP BY plan_id) AS emcee', 'emcee.plan_id = p.plan_id')
            ->join('LEFT JOIN', '(SELECT COUNT(*) AS lectuer_total,plan_id FROM ' . Participant::tableName() . ' WHERE role =\'' . Participant::TYPE_LECTUER . '\'  GROUP BY plan_id) AS lectuer', 'lectuer.plan_id = p.plan_id')
            ->join('LEFT JOIN', '(SELECT COUNT(*) AS member_total,(SUM(confirmed)) AS member_confirmed,plan_id FROM ' . Participant::tableName() . ' WHERE (role =\'' . Participant::TYPE_TRAINEE . '\' OR role = \'' . Participant::TYPE_MONITOR . '\') AND status = ' . Participant::STATUS_NORMAL . '  GROUP BY plan_id) AS mem', 'mem.plan_id = p.plan_id')
            ->join('LEFT JOIN',
                '(SELECT COUNT(*) AS sche_total,(SUM(deprecated)) AS sche_deprecated,plan_id FROM'
                . '(SELECT schedule_id,(CASE WHEN UNIX_TIMESTAMP(s.sche_bt) < UNIX_TIMESTAMP() AND UNIX_TIMESTAMP(s.sche_et) > UNIX_TIMESTAMP() THEN 1 ELSE 0 END) AS deprecated,plan_id FROM schedule AS s WHERE s.sche_status = 1) AS s2 GROUP BY plan_id) AS sche',
                'sche.plan_id = p.plan_id')
            ->join('LEFT JOIN', ModEnroll::tableName() . ' AS enroll', 'p.plan_id = enroll.plan_id');


        $query->join('LEFT JOIN', Participant::tableName() . ' AS part', 'p.plan_id = part.plan_id')->groupBy('p.plan_id');

        $queryCount = clone $query;

        $query->offset($offset)->limit($limit);

        if ($_sort) {
            foreach ($_sort as $k => $v) {
                $query->addOrderBy($v);
            }
        } else {
            $query->addOrderBy('p.plan_ct DESC')->addOrderBy('p.plan_id');
        }

        $plans = $query->asArray()->all();


        return [$plans, $queryCount->count()];
    }

    /**
     * 获取unique_code用户所在的所有计划
     * @param $unique_code
     * @param array $map
     * @param int $offset
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getMemberPlans($unique_code, array $map = [], $offset = 0, $limit = 20, $order = '')
    {
        
        $query = static::find()
            ->from(['p' => static::tableName()])
            ->join('INNER JOIN', Participant::tableName() . ' AS m', 'p.plan_id = m.plan_id')
            ->where($map);

        $query->andWhere(['unique_code' => $unique_code, 'plan_status' => 2]);

        if (-1 != $offset) $query->offset($offset);

        if (-1 != $limit) $query->limit($limit);

        if (!empty($order)) $query->orderBy($order); else $query->orderBy('plan_ct desc');

        return [$query->asArray()->all(), $query->count()];
    }

    /**
     * @param array $where
     * @return Plan
     */
    public static function getPlan(array $where = [], $asArray = false)
    {
        $query = static::find()->from(['p' => static::tableName()])
            ->select([
                'p.*',
                'emcee_total' => '(CASE WHEN ISNULL(emcee.emcee_total) THEN 0 ELSE emcee.emcee_total END)',
                'lectuer_total' => '(CASE WHEN ISNULL(lectuer.lectuer_total) THEN 0 ELSE lectuer.lectuer_total END)',
                'member_total' => '(CASE WHEN ISNULL(mem.member_total) THEN 0 ELSE mem.member_total END)',
                'member_confirmed' => '(CASE WHEN ISNULL(mem.member_confirmed) THEN 0 ELSE mem.member_confirmed END)',
                'schedule_total' => '(CASE WHEN ISNULL(sche.sche_total) THEN 0 ELSE sche.sche_total END)',
                'schedule_deprecated' => '(CASE WHEN ISNULL(sche.sche_deprecated) THEN 0 ELSE sche.sche_deprecated END)',
            ])
            ->where($where)
            ->join('LEFT JOIN', '(SELECT COUNT(*) AS emcee_total,plan_id FROM ' . Participant::tableName() . ' WHERE role =\'' . Participant::TYPE_EMCEE . '\' GROUP BY plan_id) AS emcee', 'emcee.plan_id = p.plan_id')
            ->join('LEFT JOIN', '(SELECT COUNT(*) AS lectuer_total,plan_id FROM ' . Participant::tableName() . ' WHERE role =\'' . Participant::TYPE_LECTUER . '\'  GROUP BY plan_id) AS lectuer', 'lectuer.plan_id = p.plan_id')
            ->join('LEFT JOIN', '(SELECT COUNT(*) AS member_total,(SUM(confirmed)) AS member_confirmed,plan_id FROM ' . Participant::tableName() . ' WHERE (role =\'' . Participant::TYPE_TRAINEE . '\' OR role = \'' . Participant::TYPE_MONITOR . '\') AND status = ' . Participant::STATUS_NORMAL . '  GROUP BY plan_id) AS mem', 'mem.plan_id = p.plan_id')
            ->join('LEFT JOIN',
                '(SELECT COUNT(*) AS sche_total,(SUM(deprecated)) AS sche_deprecated,plan_id FROM'
                . '(SELECT schedule_id,(CASE WHEN UNIX_TIMESTAMP(s.sche_bt) < UNIX_TIMESTAMP()  THEN 1 ELSE 0 END) AS deprecated,plan_id FROM schedule AS s) AS s2 GROUP BY plan_id) AS sche',
                'sche.plan_id = p.plan_id');

        $query->join('LEFT JOIN', Participant::tableName() . ' AS part', 'p.plan_id = part.plan_id');

        $query->groupBy('p.plan_id');

        $asArray && $query->asArray();

        $plan = $query->one();

        return $plan;
    }

    /**
     * @param $plan_id
     * @return Plan
     */
    public static function getPlanById($plan_id, $asArray = false)
    {
        $where[] = ['p.plan_id' => $plan_id];
//        $where[] = ['like', 'plan_name', '周'];
//        $where[] = ['like', 'name', 'X'];
        array_unshift($where, 'AND');
        return static::getPlan($where, $asArray);
    }


    public function deleteGroup($group_id)
    {
        if (false === ($group = TraineeGroup::find()->where(['plan_id' => $this->plan_id, 'group_id' => $group_id])->one())) return false;
        return $group->delete();
    }

    public function saveGroup($group_data)
    {
        if (isset($group_data['group_id'])) {
            if (($group = TraineeGroup::findOne($group_data['group_id'])) == null) return false;
        } else {
            $group = new TraineeGroup();
        }
        $group->setAttributes($group_data);
        $group->plan_id = $this->plan_id;
        if (false === $group->save()) return false;
        return $group->getAttributes();
    }

}













