<?php


namespace service\plan\models;

use service\sign\models\Sign;

class Schedule extends \service\base\db\ARecord
{
    /**
     * 日程状态：-1：删除，1：正常
     */
    const STATUS_DELETED = -1;
    const STATUS_NORMAL = 1;


    /**
     * 日程的类型：1：活动；2：任务
     */
    const TYPE_ACTIVITY = 1;
    const TYPE_TASK = 2;

    static public function tableName()
    {
        return '{{%schedule}}';
    }

    static public function primaryKey()
    {
        return ['schedule_id'];
    }

    public function rules()
    {
        return [
            [['sche_name', 'sign_address'], 'trim'],
            [['plan_id', 'sche_name'], 'required'],
            ['pid', 'default', 'value' => 0],
            ['plan_id', 'exist',
                'targetClass' => 'service\plan\models\Plan',
                'targetAttribute' => 'plan_id',
                'message' => 'plan not found.'],
            [['sche_bt', 'sche_et'], 'date', 'format' => 'yyyy-MM-dd HH:mm', 'on' => ['create', 'update']],
            [['sche_status'], 'default', 'value' => static::STATUS_NORMAL],
            ['sign_id', 'exist',
                'targetClass' => 'service\sign\models\Sign',
                'targetAttribute' => 'sign_id',
                'message' => 'sign was not found.'],
            ['sche_et', 'compare', 'compareAttribute' => 'sche_bt', 'operator' => '>'],
        ];
    }

    static public function getSchedules($where, $offset = null, $limit = null)
    {
        $query = static::find()->where($where);
        !is_null($offset) && $query->offset($offset);
        !is_null($limit) && $query->limit($limit);
        return $query->asArray()->all();
    }

    static public function getSchedule(array $where = [])
    {
        return static::find()->where($where)->asArray()->one();
    }

    static public function getScheduleById($schedule_id)
    {
        return static::getSchedule(['schedule_id' => $schedule_id]);
    }

    static public function getPlanSechdules($plan_id, array  $where = [], $offset = null, $limit = null)
    {
        $where['plan_id'] = $plan_id;
        return static::getSchedules($where, $offset, $limit);
    }


    static public function deleteSchedule($schedule_id)
    {
        $where = ['pid' => $schedule_id];
        $schedule = static::find()->where($where)->all();

        foreach ($schedule as $children) {
            $children->sche_status = static::STATUS_DELETED;
            $children->save();
        }

        $s = static::findOne($schedule_id);
        $s->sche_status = static::STATUS_DELETED;

        return $s->save();
    }

    /**
     * 签到
     * @return \yii\db\ActiveQuery
     * @author yangzy 2017/2/28
     */
    public function getSign()
    {
        return $this->hasOne(Sign::className(), ['sign_id' => 'sign_id']);
    }
}