<?php
/**
 * Created by PhpStorm.
 * User: Win10
 * Date: 2016/10/17
 * Time: 15:12
 */

namespace service\wsite\models;

use service\wsite\models\menuItem;
use service\wsite\models\resources;

class menus extends \service\base\db\ARecord
{
    public static function tableName()
    {
        return 'menus';
    }

    public static function getMenus($request_app_id, $menu_group)
    {
        $where['app_id'] = $request_app_id;
        $where['identifier'] = $menu_group;
        if (false == ($menuObj = static::find()->where($where)->one())) return [];
        $menus = $menuObj->toArray();

        $map = ['menu_id' => $menus['id']];
        $menuItem = menuItem::find()->where($map)->asArray()->all();
//        $menuItem = menuItem::find()->from(['m' => menuItem::tableName()])
//            ->select(['m.*', 'r.settings'])
//            ->where($map)
//            ->join('LEFT JOIN', resources::tableName() . ' AS r', 'm.url = r.id')
//            ->asArray()
//            ->all();
//
//        foreach ($menuItem as &$item) {
//            $item['settings'] = $item['settings'] ? unserialize($item['settings']) : '';
//        }

        $menuItem = static::findSon($menuItem);

        return $menuItem;
    }

    public static function findSon($menuItem, $parent_id = 0)
    {

        $arr = [];
        foreach ($menuItem as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                $arr[$key] = $item;
                $arr[$key]['son'] = static::findSon($menuItem, $item['id']);
            }
        }

        return $arr;

    }

    public static function getPage($page_id)
    {
        return resources::findOne($page_id);
    }

    public static function getWidgets(array $widgets_id = [])
    {
        $where[] = 'AND';
        $where[] = ['in', 'id', $widgets_id];
        return resources::find()->where($where)->asArray()->all();
    }
}