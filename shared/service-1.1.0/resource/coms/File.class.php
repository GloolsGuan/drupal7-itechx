<?php
namespace service\resource\coms;

use service\base\Base;
use \service\resource\models\File as ModFile;

class File extends AbstractResourceCom
{
    /**
     * 存储方式：本地文件
     */
    const STORE_TYPE = 1;
public $base_entity;
    /**
     * @param array $data ['file'=>'C:\wamp\tmp\php30D6.tmp','name' => 'uploadify.swf', 'type' => 'application/x-shockwave-flash',
     * 'size' => 12767]
     * @return \service\base\type
     */
    public function addResource(array $data = [])
    {
        $sFile = Base::loadService('\service\file\File');
        $files['file'] = $data;
        $error = $file = $sFile->addFiles($files);
        if ($this->isErrorResponse($error)) return $this->buildResponse($error['status'], $error['code'], $error['data']);
        return !empty($file) ? $file['file'] : $this->buildResponse('failed', 400, 'failed to add resource');
    }

    /**
     *
     * @param $resource_id
     * @return array|\service\base\type
     */
    public function getResource($resource_id)
    {
        $file = ModFile::find()->where(['resource_id' => $resource_id])->one();
        if (empty($file)) return $this->buildResponse('error', 400, 'file was not found');
        return $file->getAttributes();
    }

    /**
     * @param array $resource_ids
     * @return array|\service\base\type|\yii\db\ActiveRecord[]
     */
    public function getResources(array $resource_ids = [])
    {
        $where = [];
        if (!empty($resource_ids)) $where['resource_id'] = $resource_ids;
        $files = ModFile::find()->where($where)->asArray()->all();
        if (false === $files) return $this->buildResponse('failed', 400, 'failed to get files');
        return $files;
    }
}