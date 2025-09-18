<?php
/**
 *
 */

namespace service\base\helpers;

use yii\helpers\ArrayHelper as YiiArrayHelper;

class ArrayHelper extends YiiArrayHelper
{
    /**
     * 根据$map指定的键名对应关系，将二维数组$right中指定的columns列合并到二维数组$left中
     *
     * @param array $left
     * @param array $right
     * @param array $map ['左数组中的关联值'=>'右数组中的关联值']
     * @param array $columns 指定右数组中哪些列要合并到左数组中
     * @return array
     * @throws InvalidParamException
     * @author yangzy 2017/2/1
     */
    public static function  joinLeft(array $left, array $right, array $map = ['id' => 'id'], array $columns = [])
    {
        if (empty($left)) return $left;
        if (empty($right)) return $left;

        $keys = $columns;
        if (empty($columns))
            $keys = array_keys($right[0]);

        $lkey = array_keys($map);
        $leftKey = array_pop($lkey);
        $rightKey = array_shift($map);

        $keys = array_diff($keys, [$rightKey]);

        if (!array_key_exists($leftKey, $left[0]) || !array_key_exists($rightKey, $right[0])) {
            throw new InvalidParamException('The key=>value in $map was error');
        }

        $right = ArrayHelper::index($right, $rightKey);

        foreach ($left as &$item) {
            $rightItem = ArrayHelper::getValue($right, $item[$leftKey], null);
            foreach ($keys as $key) {
                $t = ArrayHelper::getValue($rightItem, $key, null);
                if (!is_null($t)) {
                    $item[$key] = $t;
                } else {
                    if (!isset($item[$key]))
                        $item[$key] = $t;
                }
            }
        }

        return $left;
    }
}