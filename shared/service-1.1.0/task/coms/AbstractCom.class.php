<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/16
 * Time: 15:44
 */

namespace service\task;


abstract class AbstractCom extends \service\base\Component
{
    abstract public function create();


    abstract public function remove();


    abstract public function getEntities();

    abstract public function update();
}