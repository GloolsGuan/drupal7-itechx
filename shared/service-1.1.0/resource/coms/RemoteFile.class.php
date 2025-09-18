<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/22
 * Time: 13:30
 */

namespace service\resource\coms;

use service\base\Base;
use \service\resource\models\RemoteFile as ModRemoteFile;


class RemoteFile extends AbstractResourceCom
{
    /**
     * 存储方式：远程文件
     */
    const STORE_TYPE = 2;
public $base_entity;
    public function addResource(array $data = [])
    {
        $file = new ModRemoteFile();
        $file->setAttributes($data);
        if (false === $file->save()) return $this->buildResponse('failed', 400, 'failed to save remote file');
        return $file->getAttributes();
    }

    public function getResource($resource_id)
    {
        $file = ModRemoteFile::find()->where(['resource_id' => $resource_id])->one();
        if (empty($file)) return $this->buildResponse('error', 400, 'remote file was not found');
        return $file->getAttributes();
    }

    public function getResources(array $resource_ids = [])
    {
        $where = [];
        if (!empty($resource_ids)) $where['resource_id'] = $resource_ids;
        $files = ModRemoteFile::find()->where($where)->asArray()->all();
        if (false === $files) return $this->buildResponse('failed', 400, 'failed to get remote files');
        return $files;
    }
}