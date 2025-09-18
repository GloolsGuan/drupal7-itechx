<?php
/**
 *
 */

namespace service\plan;

use service\base\Module;
use service\plan\interfaces\Statistics as StatisticsInterface;

class Statistics extends Module implements StatisticsInterface
{
    public function overview($planId)
    {
        $data = [
            'id' => 1,//计划id
            'views' => 1000,//计划的浏览量
            'confirmed' => 80,//确认参与计划人数
            'participants' => 100,//参与计划人数
            'schedules' => [//计划的日程信息，不包括子日程
                [
                    'id' => 1,//日程id
                    'name' => '日程1',//日程名称
                    'signCount' => 60,//签到人数
                ]
            ],
            'tasks' => [//计划关联的调研、资料等统计情况
                'survey' => [//调研信息
                    [
                        'title' => '调研1',
                        'participants' => 50//参与人数
                    ],
                    [
                        'title' => '调研2',
                        'participants' => 60//参与人数
                    ]
                ],
                'document' => [//资料信息，暂不处理
                    [
                        'title' => '调研1',
                        'participants' => 50//参与人数
                    ],
                    [
                        'title' => '调研2',
                        'participants' => 60//参与人数
                    ]
                ]
            ]
        ];

        return $this->buildResponse('success', 201, $data);
    }

    public function userStatistics($planId, array $userIds = [], $name = '')
    {
        
        if (empty($planId)) return $this->buildResponse('error', 400, '$planId cannot be empty');
        
        $connection  = \Yii::$app->db;
        
        //获取统计用户
        $sql     = "SELECT participant.name,trainee_group.group_id,
                           trainee_group.group_name,participant.unique_code,participant.credit
                    FROM `participant`
                    LEFT JOIN `trainee_group` ON trainee_group.plan_id=participant.plan_id
                    WHERE `participant`.`plan_id`='$planId'";
        $command = $connection->createCommand($sql);
        $participant = $command->queryAll();
        
        //====== 用户签到统计开始  ======
        
         //获取日程id
         $sql     = "SELECT sign_id
                     FROM `schedule`
                     WHERE `schedule`.`plan_id`='$planId'
                     AND `schedule`.`sign_id` IS NOT NULL
                     AND `schedule`.`sign_type` <> 0";
         $command = $connection->createCommand($sql);
         $schedules = $command->queryAll();
        
         $sign_ids = '';//签到id字符
         foreach ($schedules as $k => $v){
             $sign_ids .= $v['sign_id'].',';
         }
         $sign_ids = rtrim($sign_ids,',');
        
         empty($sign_ids) ? $sign_ids = '0' : NULL;
         
         //获取日程签到数量
         $sql     = "SELECT unique_code,COUNT(*) AS sign_num 
                     FROM `sign_detail`
                     WHERE `sign_id` in ($sign_ids)
                     GROUP BY `unique_code`";
         $command = $connection->createCommand($sql);
         $rows = $command->queryAll();
        $sign_detail = [];
        foreach($rows as $k => $v){
            $sign_detail[$v['unique_code']] = ['sign_num' => $v['sign_num']];
        }
        //====== 签到统计结束 ======
        
        //====== 计划日程应该签到天数开始 ======
         $sql     = "SELECT unique_code,COUNT(*) AS confirmed
                     FROM `participant`
                     WHERE `plan_id`='$planId' AND `confirmed`=1";
         $command = $connection->createCommand($sql);
         $confirmed_num = $command->queryAll();
         $confirmed_num = isset($confirmed_num[0]['confirmed']) ? $confirmed_num[0]['confirmed'] : 0;
        //====== 计划日程应该签到天数结束 ======
        
        //数据合并处理
        $totalSignCount = 0;
        $new_participant = [];
        foreach ($participant as $k => $v){
            $new_participant[$k]['name'] = $v['name'];//姓名
            $new_participant[$k]['group'] = ['id' => $v['group_id'],'name' => $v['group_name']];
            //所属小组
            $new_participant[$k]['signCount'] = isset($sign_detail[$v['unique_code']]) ? $sign_detail[$v['unique_code']]['sign_num'] : $totalSignCount++;
            $new_participant[$k]['downloadCount'] = 0;//下载资料数
            $new_participant[$k]['surveyCount'] = 0;//调研次数
            $new_participant[$k]['credit'] = $v['credit'];//积分
        }
        
        $data = [
            'id' => $planId,//计划id
            'signCount' => $confirmed_num,//计划中含有的签到总数
            'participants' => $new_participant
        ];
        
        return $this->buildResponse('success', 201, $data);
    }
    
    public function userGroupStatistics($planId)
    {
        
        if (empty($planId)) return $this->buildResponse('error', 400, '$planId cannot be empty');
        
        $connection  = \Yii::$app->db;
        
        //获取统计用户
        $sql     = "SELECT participant.participant_id AS id,rs_trainee_group.group_id,participant.name,participant.credit
                    FROM `participant`
                    LEFT JOIN `rs_trainee_group` ON rs_trainee_group.participant_id=participant.participant_id
                    WHERE `participant`.`plan_id`='$planId'";
        $command = $connection->createCommand($sql);
        $rows = $command->queryAll();
        
        $participant = [];
        foreach($rows as $k => $v){
            $participant[$v['group_id']][] = ['id' => $v['id'],'name' => $v['name'],'credit' => $v['credit']];
        }
        
        //小组积分排名
        
        
        //获取小组信息
        $sql     = "SELECT trainee_group.*,SUM(`participant`.credit) AS credit
                    FROM `participant`
                    LEFT JOIN `rs_trainee_group` ON rs_trainee_group.participant_id=participant.participant_id
                    LEFT JOIN `trainee_group` ON trainee_group.group_id=rs_trainee_group.group_id
                    WHERE `participant`.`plan_id`='$planId'
                    GROUP BY `rs_trainee_group`.`group_id`
                    ORDER BY `credit` DESC;";
        $command = $connection->createCommand($sql);
        $trainee_group = $command->queryAll();
        
        //封装数据
        $new_groups = [];
        $rank = 1;
        foreach($trainee_group as $k => $v){
            $new_groups[$k]['name'] = $v['group_name'];
            $new_groups[$k]['rank'] = $rank++;
            $new_groups[$k]['credit'] = $v['credit'];
            $new_groups[$k]['participants'] = isset($participant[$v['group_id']]) ? $participant[$v['group_id']] : NULL;
        }
        
        $data = [
            'id' => $planId,//计划id
            'groups' => $new_groups//计划参与人员分组
        ];
        return $this->buildResponse('success', 201, $data);
    }

    /**
     * 统计签到
     */
    public function signStatistics($planId)
    {
        
        if (empty($planId)) return $this->buildResponse('error', 400, '$planId cannot be empty');
        
        $sign_type_conf = [1 => '手写签到',2 => '快捷签到'];
        
        $connection  = \Yii::$app->db;
        
        //获取日程信息
        $sql     = "SELECT schedule_id,sign_id,sign_type,sche_name
                    FROM `schedule`
                    WHERE `schedule`.`plan_id`='$planId'
                    AND `schedule`.`sign_id` IS NOT NULL
                    AND `schedule`.`sign_type` <> 0";
        $command = $connection->createCommand($sql);
        $schedules = $command->queryAll();
        
        $sign_types = [];
        $sign_ids = '';//签到id字符
        foreach ($schedules as $k => $v){
            $sign_ids .= $v['sign_id'].',';
            $sign_types[] = $v['sign_type'];
        }
        $sign_ids = rtrim($sign_ids,',');
        
        empty($sign_ids) ? $sign_ids = '0' : NULL;
        
        //获取日程签到数量
        $sql     = "SELECT sign_id,COUNT(*) AS sign_num 
                    FROM `sign_detail`
                    WHERE `sign_id` in ($sign_ids)
                    GROUP BY `sign_id`";
        $command = $connection->createCommand($sql);
        $rows = $command->queryAll();
        
        $sign_detail = [];
        foreach ($rows as $k => $v){
            $sign_detail[$v['sign_id']] = $v['sign_num'];
        }
        
        //获取参与人数
        $sql     = "SELECT COUNT(*) AS participants
                    FROM `participant`
                    WHERE `plan_id`='$planId'";
        $command = $connection->createCommand($sql);
        $participant_num = $command->queryAll();
        $participant_num = isset($participant_num[0]['participants']) ? $participant_num[0]['participants'] : 0;
        
        //获取确认参与人数
        $sql     = "SELECT COUNT(*) AS confirmed
                    FROM `participant`
                    WHERE `plan_id`='$planId' AND `confirmed`=1";
        $command = $connection->createCommand($sql);
        $confirmed_num = $command->queryAll();
        $confirmed_num = isset($confirmed_num[0]['confirmed']) ? $confirmed_num[0]['confirmed'] : 0;
        
        $new_schedules = [];
        //数据封装
        foreach ($schedules as $k => $v){
            $new_schedules[$k]['id'] = $v['schedule_id'];
            $new_schedules[$k]['signType'] = $v['sign_type'];
            $new_schedules[$k]['name'] = $v['sche_name'];
            $new_schedules[$k]['signCount'] = isset($sign_detail[$v['sign_id']]) ? $sign_detail[$v['sign_id']] : 0;
        }
        
        $data = [
            'id' => $planId,//计划id
            'views' => 1000,//计划的浏览量
            'confirmed' => $confirmed_num,//确认参与计划人数
            'participants' => $participant_num,//参与计划人数
            'schedules' => $new_schedules //计划的日程信息，不包括子日程
        ];
        
        return $this->buildResponse('success', 201, $data);
    }
    
    /**
     * 日程统计信息
     */
    public function scheduleStatistics($scheduleId,array $map = [])
    {
        
        $sign_type_conf = [1 => '手写签到',2 => '快捷签到'];
        
        $connection  = \Yii::$app->db;
        
        //获取日程信息
        $sql     = "SELECT schedule_id,plan_id,sign_id,sign_type,sche_name,sche_bt,sche_et,sign_address
                    FROM `schedule`
                    WHERE `schedule`.`schedule_id`='$scheduleId'";
        $command = $connection->createCommand($sql);
        $schedule = $command->queryAll();
        $schedule = isset($schedule[0]) ? $schedule[0] : [];
        
        if(empty($schedule)) return $this->buildResponse('error', 400, 'schedule does not exist');
        
        //获取日程签到数量
        $sql     = "SELECT COUNT(*) AS sign_num
                    FROM `sign_detail`
                    WHERE `sign_id` = " . $schedule['sign_id'];
        $command = $connection->createCommand($sql);
        $sign_num = $command->queryAll();
        $sign_num = isset($sign_num[0]) ? $sign_num[0]['sign_num'] : 0;
        
        //获取参与人员列表
        $sql     = "SELECT *
                    FROM `participant`
                    WHERE `plan_id`='".$schedule['plan_id']."'";
        
        //签到类型处理
        if(isset($map['sign_type'])){
            if(1 == $map['sign_type']){
                $sql .= ' AND confirmed = 1';
            }elseif(3 == $map['sign_type']){
                $sql .= ' AND confirmed = 0';
            }else{
                $sql .= ' AND confirmed != -1';
            }
        }
        
        $command = $connection->createCommand($sql);
        $participants = $command->queryAll();
        
        //获取签到人员列表
        $sql     = "SELECT *
                    FROM `sign_detail`
                    WHERE `sign_id`='".$schedule['sign_id']."'";
        
        $command = $connection->createCommand($sql);
        $rows = $command->queryAll();
        
        $sign_details = [];
        foreach ($rows as $k => $v){
            $sign_details[$v['unique_code']] = $v;
        }
        
        //合并参与人员列表与签到人员列表
        $participants_num = 0;//日程的参与人数，目前等于计划的参与人数
        $confirmed_num = 0;//确认参与计划人数
        $new_participants_data = [];
        foreach($participants as $k => $v){
            
            $participants_num++;
            1 == $v['confirmed'] ? $confirmed_num++ : null;
            
            if(isset($map['sign_type']) && 2 == $map['sign_type'] && isset($sign_details[$v['unique_code']])){
                //签到类型为2，不记录数据
            }else{
                $new_participants_data[$k]['id'] = $v['unique_code'];
                $new_participants_data[$k]['name'] = $v['name'];
                $new_participants_data[$k]['corporation'] = 'corporation';
                $new_participants_data[$k]['department'] = 'department';
                $new_participants_data[$k]['position'] = 'position';
                $new_participants_data[$k]['signDate'] =  isset($sign_details[$v['unique_code']]) ? $sign_details[$v['unique_code']]['create_time'] : '';
                $new_participants_data[$k]['signAddress'] =  isset($sign_details[$v['unique_code']]) ? $sign_details[$v['unique_code']]['address'] : '';
                $new_participants_data[$k]['signPicture'] =  isset($sign_details[$v['unique_code']]) ? $sign_details[$v['unique_code']]['draw_url'] : '';
                $new_participants_data[$k]['meta'] =  isset($sign_details[$v['unique_code']]) ? $sign_details[$v['unique_code']]['meta'] : '';
            }
            
        }

        $data = [
            'id' => $scheduleId,//日程id
            'participants' => $participants_num,//日程的参与人数，目前等于计划的参与人数
            'signType' => $schedule['sign_type'],//签到方式,0：不签到，1：手写签到，2：快捷签到
            'confirmed' => $confirmed_num,//确认参与计划人数
            'signed' => $sign_num,//该日程签到人数
            'beginTime' => $schedule['sche_bt'],//该日程开始时间
            'endTime' => $schedule['sche_et'],//该日程结束时间
            'signAddress' => $schedule['sign_address'],//日程签到地点
            'participantsData' => $new_participants_data//人员的信息
        ];
        
        return $this->buildResponse('success', 201, $data);
    }

    public function tasksStatistics($planId)
    {
        $data = [
            'id' => 1,//计划id
            'participants' => 100,//计划的参与人数
            'survey' => [//关联的调研
                [
                    'id' => 1,//调研id
                    'title' => '小调查',//调研名称
                    'doneCount' => 20,//完成人数
                ],
                [
                    'id' => 2,//调研id
                    'title' => '调查2',//调研名称
                    'doneCount' => 30,//完成人数
                ]
            ],
        ];
        return $this->buildResponse('success', 201, $data);
    }

    public function taskStatistics($taskId)
    {
        $data = [
            'id' => 1,//任务id
            'name' => '调查研究',//任务名称
            'participants' => 100,//任务的参与人数，目前等于计划的参与人数
            'participantsData' => [//关联的调研
                [
                    'id' => 1,//用户id
                    'name' => '小王',//用户名称
                    'doneStatus' => 1,//任务完成发问，1：已完成，0：未完成
                    'doneAt' => '2016-11-07 10:30:59',//完成时间
                ],
                [
                    'id' => 2,//用户id
                    'name' => '小王2',//用户名称
                    'doneStatus' => 0,//任务完成发问，1：已完成，0：未完成
                    'doneAt' => '2016-10-07 10:30:59',//完成时间
                ],
            ],
        ];
        return $this->buildResponse('success', 201, $data);
    }

    public function surveysStatistics($planId)
    {
        $data = [
            'id' => 1,//计划id
            'participants' => 100,//计划的参与人数
            'survey' => [//关联的调研
                [
                    'id' => 1,//调研id
                    'title' => '小调查',//调研名称
                    'doneCount' => 20,//完成人数
                ],
                [
                    'id' => 2,//调研id
                    'title' => '调查2',//调研名称
                    'doneCount' => 30,//完成人数
                ]
            ],
        ];
        return $this->buildResponse('success', 201, $data);
    }

    public function surveyStatistics($surveyId)
    {
        $data = [];
        return $this->buildResponse('success', 201, $data);
    }

    public function orderStatistics($planId,array $search = [], $department = '')
    {

        if (empty($planId)) return $this->buildResponse('error', 400, '$planId cannot be empty');

        $connection = \Yii::$app->db;

        //获取订单数据
        $sql = "SELECT biz_order.id,biz_order.unique_code,participant.name,biz_order.order_no,biz_order.create_time,
                           biz_order.course_id,biz_order.amount,biz_order.reason,
                           biz_payment.paymode
                    FROM `biz_order`
                    LEFT JOIN `participant` ON participant.unique_code=biz_order.unique_code
                    LEFT JOIN `biz_payment` ON biz_payment.order_id=biz_payment.id
                    WHERE `biz_order`.`course_id`='$planId'";
        $command = $connection->createCommand($sql);
        $order = $command->queryAll();

        if (empty($order)) return $this->buildResponse('error', 400, 'order does not exist');

        $new_order = [];
        foreach ($order as $k => $v) {
            if (!empty($v['course_id'])) {
                $new_order[$v['course_id']][] = ['id' => $v['id'],
                    'order_no' => $v['order_no'],
                    'userid' => $v['unique_code'],
                    'username' => $v['name'],
                    'createAt' => $v['create_time'],
                    'amount' => $v['amount'],
                    'reason' => $v['reason']
                ];
            }
        }

        $status = empty($new_order) ? 200 : 201;

        return $this->buildResponse('success', $status, $new_order);
    }

    public function creditStatistics($planId, array $userIds = [], $name = '')
    {
        $data = [
            'id' => 1,//计划id
            'participants' => [//计划学员
                [
                    'id' => 'lkdairna52ikssd',//用户id
                    'name' => '小王',//用户名
                    'group' => [//交易日期
                        'id' => 1,//小组id
                        'name' => 'A组'
                    ],
                    'courseCredit' => 10,//课程评分
                    'surveyCredit' => 15,//调研评分
                    'totalCredit' => 210,//总学分
                ]
            ]
        ];
        return $this->buildResponse('success', 201, $data);
    }

    public function planStatistics($nameSearch = '', $offset = 0, $limit = 20)
    {
        $data = [
            [
                'id' => 1,
                'title' => '活动名1',//活动名称
                'createTime' => '2016-12-08 11:02:58',//创建时间
                'updateTime' => '2016-12-08 11:02:58',//更新时间
                'beginTime' => '2016-12-08 11:02:58',//开始时间
                'endTime' => '2016-12-08 11:02:58',//结束时间
                'type' => 1,//活动类型
                'address' => '活动地址',//活动地址
                'credit' => 10,//课程学分
                'memberCount' => 90,//成员数
                'memberLimited' => 100,//课程名额
                'payCount' => 110,//缴费人数
                'refundCount' => 20,//退费人数
                'payTotal' => 1002000,//总收入
                'refundTotal' => 2000,//总退费
            ],
            [
                'id' => 2,
                'title' => '活动名2',//活动名称
                'createTime' => '2016-12-08 11:02:58',//创建时间
                'updateTime' => '2016-12-08 11:02:58',//更新时间
                'beginTime' => '2016-12-08 11:02:58',//开始时间
                'endTime' => '2016-12-08 11:02:58',//结束时间
                'type' => 1,//活动类型
                'address' => '活动地址',//活动地址
                'credit' => 10,//课程学分
                'memberCount' => 90,//成员数
                'memberLimited' => 100,//课程名额
                'payCount' => 110,//缴费人数
                'refundCount' => 20,//退费人数
                'payTotal' => 1002000,//总收入
                'refundTotal' => 2000,//总退费
            ],
        ];

        return $this->buildResponse('success', 201, $data);
    }

}