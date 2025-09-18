<?php
namespace service\resource;


use service\resource\models\ResourceTag;

class Tag extends \service\base\Module
{
    /**
     * @param array $map
     * @return mixed
     */
    public function getTags(array $map = [])
    {
        $tag = ResourceTag::getTags($map);

        return $tag;
    }
}