<?php


namespace service\base\db;

use  \service\base\db\ARecord;

/**
 * Class SimpleAR
 * @package service\base\db
 * @design yangzy 20160901
 * @author yangzy 20160901
 */
class SimpleAR extends ARecord
{
    const STATUS_DELETED = -1;
    const STATUS_NORMAL = 1;

    public static function primaryKey()
    {
        return ['id'];
    }

    public static function statusKey()
    {
        return 'status';
    }

    public static function createItem(array $data = [])
    {
        $data[static::statusKey()] = static::STATUS_NORMAL;

        $item = new static();
        $item->setAttributes($data);

        if (false === $item->save()) {
            return false;
        }

        if (false === $item->save()) {
            return false;
        }

        return $item;
    }

    public static function deleteItems(array $where = [])
    {
        return static::updateAll([static::statusKey() => static::STATUS_DELETED], $where);
    }

    /**
     * @deprecated
     * @param array $ids
     * @return int
     */
    public static function deleteItemById(array $ids = [])
    {
        return static::deleteItemsById($ids);
    }

    public static function deleteItemsById(array $ids = [])
    {
        return static::deleteItems([static::primaryKey()[0] => $ids]);
    }

    public static function getItems(array $where, $offset = -1, $limit = -1, $order = '')
    {
        $query = static::find()->where($where);

        if (-1 != $offset) $query->offset($offset);

        if (-1 != $limit) $query->limit($limit);

        if (!empty($order)) $query->orderBy($order);

        return $query->asArray()->all();
    }

    public static function getItemsByid(array $ids = [])
    {
        return static::getItems([static::primaryKey()[0] => $ids]);
    }

    public static function getItem(array $where = [])
    {
        return static::find()->where($where)->one();
    }

    public static function getItemById($id)
    {
        return static::getItem([static::primaryKey()[0] => $id]);
    }
}