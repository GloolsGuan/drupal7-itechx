<?php
/**
 *
 */

namespace service\log\models;

use service\base\db\SimpleAR;

/**
 * Class Action
 * @package service\log\models
 * @author yangzy 20161214
 */
class Action extends SimpleAR
{
    public static function tableName()
    {
        return 'lg_action';
    }
}