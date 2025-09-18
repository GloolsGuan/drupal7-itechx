<?php
namespace service\base\traits;
use Yii;

/**
 * 后台搜索专用
 * @author laughstorm
 *
 */
trait SearchModelTrait{

    public function search($filters){

        $query = static::find();
        $params = Yii::$app->getRequest()->getQueryParams();

        if(count($filters) == 0 || !isset($params[$this->formName()])){
            return $query;
        }
        $this->load($params);

        foreach ($filters as $filter) {
            if (strncmp($filter, "%", 1) == 0) {
                $filter = substr($filter, 1);
                $query->andFilterWhere([
                    'like',
                    $filter,
                    $this->{$filter}
                ]);
            } else {
                $query->andFilterWhere([
                    $filter => $this->{$filter}
                ]);
            }
        }
        return $query;
    }
}
?>