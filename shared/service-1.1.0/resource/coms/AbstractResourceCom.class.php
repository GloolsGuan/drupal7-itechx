<?php

namespace service\resource\coms;

abstract class AbstractResourceCom extends \service\base\Module
{
    abstract public function addResource(array $data = []);

    abstract public function getResource($resource_id);
}
