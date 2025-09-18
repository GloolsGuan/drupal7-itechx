<?php
/**
 *
 */

namespace service\survey\interfaces;

/**
 * Interface LimeSurvey
 * 使用limesurvey调研问卷系统中相关表
 *
 * @package service\survey
 * @design yangzy 20161201
 * @author shenf 20161211
 */
interface LimeSurvey
{
    /**
     * 获取调研
     *
     * @param array $map 过滤条件，格式参照yii2 where()参数
     * @param int $offset 偏移量
     * @param int $limit 数据条数
     * @return array 成功时返回调研列表数据 【1】
     */
    public function getSurveys(array $map = [], $offset = -1, $limit = -1);

    /**
     * 获取指定的调查问卷
     *
     * @param array $ids 问卷id组成的数组
     * @return array 【1】调研列表
     */
    public function getSurveysById(array $ids);

    /**
     * 获取指定的调查问卷详情
     *
     * @param array $ids 问卷id
     * @return array【1】调研详情
     */
    public function getSurveyById($id);
}