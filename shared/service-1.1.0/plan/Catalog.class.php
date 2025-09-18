<?php

namespace service\plan;

use \service\base\Module;
use \service\plan\models\Catalog as ModCatalog;
use \service\plan\models\RsPlanCatalog as ModRsPlanCatalog;
use \service\plan\interfaces\Catalog as CatalogInterface;
use \service\plan\interfaces\CatalogSearch as CatalogSearchInterfaces;

class Catalog extends Module implements CatalogInterface, CatalogSearchInterfaces
{
    /**
     * 添加新目录
     *
     * @param string $name 目录名称 不能为空
     * @param int $pid 父目录ID，为0时表示添加到根目录
     * @return array 成功时返回新添加的目录数据，使用统一返回值格式
     * @design yangzy 20161120
     * @author drce 20161120
     */
    public function addItem($name, $pid = 0)
    {
        if (empty($name)) return $this->buildResponse('error', 400, '$name cannot be empty');

        if (0 != $pid) {//存在父级目录，判断父目录ID是否存在，不存在报错
            if (NULL === ($catalog = ModCatalog::find()->where(['id' => $pid])->one())) return $this->buildResponse('failed', 400, '$pid does not exist');
        }

        $model = new ModCatalog();
        $model->setAttributes(['name' => $name, 'pid' => $pid, 'status' => ModCatalog::STATUS_NORMAL]);

        if (false === $model->save()) {
            return $this->buildResponse('failed', 400, 'failed to add Catalog resource');
        } else {

            $new_catalog = $model->getAttributes();

            if (0 != $pid) {//存在父级目录
                $model->setAttributes(['xpath' => $catalog['xpath'] . '/' . $new_catalog['id']]);
            } else {
                $model->setAttributes(['xpath' => $new_catalog['id']]);
            }

            if (false === $model->save()) return $this->buildResponse('failed', 400, 'failed to add Catalog resource');

            return $this->buildResponse('success', 201, $model->getAttributes());
        }
    }

    /**
     * 删除指定的目录（将目录的状态标记为删除）
     *
     * @param int $id 目录的id
     * @return boolean|array 成功时返回true
     * @design yangzy 20161120
     * @author drce 20161120
     */
    public function deleteItem($id)
    {
        if (0 === $id) return $this->buildResponse('error', 400, '$id cannot be empty');

        if (NULL === ($catalog = ModCatalog::find()->where(['id' => $id])->one())) return $this->buildResponse('failed', 400, 'catalog does not exist');

        $catalog->setAttributes(['status' => ModCatalog::STATUS_DELETED]);

        if (false === $catalog->save())
            return $this->buildResponse('failed', 400, 'failed to add Catalog resource');
        else
            return true;
    }

    /**
     *修改目录属性
     *
     * @param $id 要修改的目录的id
     * @param array $attributes 目录新的属性，不能为空
     * @return boolean|array 成功时返回true
     * @design yangzy 20161120
     * @author drce 20161120
     */
    public function editItem($id, array $attributes = [])
    {
        if (empty($id)) return $this->buildResponse('error', 400, '$id cannot be empty');
        if (empty($attributes) || !is_array($attributes)) return $this->buildResponse('error', 400, '$attributes must be an array');

        if (NULL === ($catalog = ModCatalog::find()->where(['id' => $id])->one())) return $this->buildResponse('failed', 400, 'catalog does not exist');

        $catalog->setAttributes($attributes);

        if (false === $catalog->save()) return $catalog->buildResponse('failed', 400, 'failed to edit Catalog resource');

        //目录修改处理
        //if (isset($attributes['']))

        return true;
    }

    /**
     * 查询目录的属性数据
     *
     * @param int $id 目录的id
     * @return array 统一格式
     * @design yangzy 20161120
     * @author drce 20161120
     */
    public function getItem($id)
    {
        if (empty($id)) return $this->buildResponse('error', 400, '$id cannot be empty');

        if (NULL === ($catalog = ModCatalog::find()->where(['id' => $id])->one())) return $this->buildResponse('failed', 400, 'catalog does not exist');

        $status = empty($rows = $catalog->getAttributes()) ? 200 : 201;

        return $this->buildResponse('success', $status, $rows);
    }

    /**
     * 获取课程体系列表
     *
     * @param array $map 查询条件，格式参照yii2 where()方法
     * @param int $offset 数据偏移量
     * @param int $limit 条数限制
     * @return array 订单列表数据 统一格式
     * @author drce 20161123
     */
    public function getItems(array $map = [], $offset = 0, $limit = 20)
    {
        $catalogs = ModCatalog::getItems($map, $offset, $limit);

        $status = empty($catalogs) ? 200 : 201;

        return $this->buildResponse('success', $status, $catalogs);

    }

    public function getall($map)
    {
        $catalogs = ModCatalog::getall($map);

        $status = empty($catalogs) ? 200 : 201;

        return $this->buildResponse('success', $status, $catalogs);

    }

    /**
     * 查询子（孙）目录（树型层级结构）
     *
     * @param int $pid 父级目录的id
     * @param bool $children 为true时，返回子孙目录；为false时，只返回子目录
     * @return array 统一格式
     * @design yangzy 20161120
     * @author drce 20161120
     */
    public function getSubitems($pid, $children = false)
    {

        if (0 === $pid)
            $map = $children ? [] : ['pid' => $pid];
        else {
            if (NULL === ($catalog = ModCatalog::find()->where(['id' => $pid])->one())) return $this->buildResponse('failed', 400, 'catalog does not exist');
            $map = ['between', 'xpath', $catalog['xpath'] . '/', $catalog['xpath'] . '0'];
        }

        $query = ModCatalog::find()->from(['catalog' => ModCatalog::tableName()])
            ->select([
                'catalog.*',
            ])
            ->andWhere(['status' => 1]);

        $children ? NULL : $query->andWhere(['pid' => $pid]);

        if (empty($rows = $query->asArray()->all()))
            $status = 200;
        else {
            $status = 201;
        }

        $s = '$rows';
        foreach ($rows as $key => $val) {
            $x = explode('/', $val['xpath']);
            foreach ($x as $k => $v) $s .= "[$v]";
            $s .= ' = ' . var_export($val, true) . ';$rows';
        }
        eval('$rows=[];' . rtrim($s, '$rows'));

        return $this->buildResponse('success', $status, $rows);
    }

    /**
     * 给目录添加课程
     *
     * @param int $catalogId 目录id
     * @param int|array $planId 课程id或课程id组成的数组
     * @return array 成功时返回true
     * @design yangzy 20161120
     * @author drce 20161120
     */
    public function addPlan($catalogId, $planId)
    {
        if (empty($catalogId)) return $this->buildResponse('error', 400, '$catalogId cannot be empty');
        if (empty($planId)) return $this->buildResponse('error', 400, '$planId cannot be empty');

        if (is_array($planId)) {
            foreach ($planId as $k) {
                $rows[] = ['catalog_id' => $catalogId, 'plan_id' => $k];
            }
        } else {
            $rows[] = ['catalog_id' => $catalogId, 'plan_id' => $planId];
        }

        if (FALSE === \Yii::$app->db->createCommand()->batchInsert(ModRsPlanCatalog::tableName(), ['catalog_id', 'plan_id'], $rows)->execute()) return $this->buildResponse('failed', 400, 'failed to add RsPlayCatalog resource');

        return true;
    }

    /**
     * 从目录中移除课程
     *
     * @param $catalogId 目录id
     * @param int|array $planId 课程id或课程id组成的数组
     * @return 成功时返回true
     * @design yangzy 20161120
     * @author drce 20161120
     */
    public function removePlan($catalogId, $planId)
    {
        if (empty($catalogId)) return $this->buildResponse('error', 400, '$catalogId cannot be empty');
        if (empty($planId)) return $this->buildResponse('error', 400, '$planId cannot be empty');

        $deleteParm = array();

        if (is_array($planId)) {
            $deleteParm = [['and', 'catalog_id = :catalogId', ['in', 'plan_id', $planId]], [':catalogId' => $catalogId]];
        } else {
            $deleteParm = [['and', 'catalog_id = :catalogId', 'plan_id = :planId'], [':catalogId' => $catalogId, ':planId' => $planId]];
        }

        if (FALSE === ModRsPlanCatalog::deleteAll($deleteParm[0], $deleteParm[1])) return $this->buildResponse('failed', 400, 'failed to remove RsPlayCatalog resource');
        
    }

    /**
     * 列举目录下的课程
     *
     * @param int $catalogId 课程id
     * @return array 统一格式
     * @design yangzy 20161120
     * @author drce 20161120
     */
    public function listCatalogPlan($catalogId)
    {
        if (empty($catalogId)) return $this->buildResponse('error', 400, '$catalogId cannot be empty');

        $query = ModRsPlanCatalog::find()->from(['plan_catalog' => ModRsPlanCatalog::tableName()])
            ->select([
                'plan_catalog.*',
            ])
            ->where(['catalog_id' => $catalogId])
            ->groupBy('plan_catalog.id');

        $status = empty($rows = $query->asArray()->all()) ? 200 : 201;

        return $this->buildResponse('success', $status, $rows);
    }

    /**
     * 查询节点
     *
     * @param string $name 节点名称
     * @return array 统一格式
     */
    public function searchbyname($name, $offset = 0, $limit = 20)
    {
        $map = [];
        if ($name != "") {
            $map = ["like", "name", $name];
        }
        $query = ModCatalog::find()->from(['catalog' => ModCatalog::tableName()])
            ->select([
                'catalog.*',
            ])
            ->where($map);

        if (empty($rows = $query->offset($offset)->limit($limit)->asArray()->all()))
            return [];
        else {
            return $rows;
        }

    }

    /**
     * 搜索课程目录
     * @param array $map 搜索条件 格式参照yii2 where方法的参数
     * @return 成功时返回匹配到的课程目录组成的列表 [1]
     * @author drce 20161129
     */
    public function search(array $map = [])
    {

        $query = ModCatalog::find()->from(['catalog' => ModCatalog::tableName()])
            ->select(['catalog.*'])
            ->where($map);

        $catalog = $query->asArray()->all();

        if (empty($catalog)) {

            $status = 200;
            return $this->buildResponse('success', $status, []);

        } else {
            $status = 201;

            //xpath自然排序
            $temp = array();
            $temp_rows = array();

            foreach ($catalog as $key => $val) {
                $temp = array_merge_recursive($temp, $val);
            }

            $xpath_flip = array_flip($temp['xpath']);
            $xpath_temp = $temp['xpath'];

            natsort($xpath_temp);

            foreach ($xpath_temp as $key => $val) {
                $temp_rows[] = ['id' => $temp['id'][$xpath_flip[$val]],
                    'pid' => $temp['pid'][$xpath_flip[$val]],
                    'name' => $temp['name'][$xpath_flip[$val]],
                    'status' => $temp['status'][$xpath_flip[$val]],
                    'xpath' => $temp['xpath'][$xpath_flip[$val]]
                ];
            }

            return $this->buildResponse('success', $status, $temp_rows);

        }


    }
    
    /**
     * 获取课程计划所关联的课程目录
     *
     * @param int $planId 计划id
     * @return array 返回课程目录数据 [1]
     * @design yangzy 20161206
     * @author shenf 20161207
     */
    public function listPlanCatalogs($planId)
    {
        if (empty($planId)) return $this->buildResponse('error', 400, '$planId cannot be empty');

        $query = ModRsPlanCatalog::find()->from(['rs_plan_catalog' => ModRsPlanCatalog::tableName()])
        ->select([
            '`rs_plan_catalog`.*,`pl_catalog`.*'
        ])
        ->join('LEFT JOIN', ModCatalog::tableName() . ' AS pl_catalog', 'rs_plan_catalog.catalog_id = pl_catalog.id')
        ->where(['rs_plan_catalog.plan_id' => $planId]);
        
        $status = empty($rows = $query->asArray()->all()) ? 200 : 201;

        return $this->buildResponse('success', $status, $rows);
    }
    
    /**
     * 移除课程计划所关联的课程目录
     *
     * @param int $planId 课程计划id
     * @param int|array $catalogIds 课程目录id或课程目录id组成的数组
     * @return array 成功时返回true
     * @design yangzy 20161206
     * @author shenf 20161207
    */
    public function removePlanCatalogs($planId, $catalogIds)
    {
        if (empty($planId)) return $this->buildResponse('error', 400, '$planId cannot be empty');
        if (empty($catalogIds)) return $this->buildResponse('error', 400, '$catalogIds cannot be empty');
        
        $deleteParm = array();
        
        if (is_array($catalogIds)) {
            $deleteParm = [['and', 'plan_id = :planId', ['in', 'catalog_id', $catalogIds]], [':planId' => $planId]];
        } else {
            $deleteParm = [['and', 'plan_id = :planId', 'catalog_id = :catalogIds'], [':planId' => $planId,':catalogIds' => $catalogIds]];
        }
        
        if (FALSE === ModRsPlanCatalog::deleteAll($deleteParm[0], $deleteParm[1])) return $this->buildResponse('failed', 400, 'failed to remove RsPlayCatalog resource');
        
    }

}