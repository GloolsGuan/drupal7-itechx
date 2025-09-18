<?php


namespace service\file;

use service\base\Module;
use service\file\interfaces\File as FileInterface;
use service\file\models\File as ModFile;

class File extends Module implements FileInterface
{
    protected $vendor = 'upload/data';

    public function __construct($id, $parent = null, $config = [])
    {
        isset($config['vendor']) && $this->vendor = trim($config['vendor']);
        parent::__construct([]);
    }

    public function getVendorPath()
    {
        return $this->vendor;
    }

    /**
     * 向文件系统添加文件
     * @param $files 被添加的文件的信息组成的二维数组 ['key1'=>['file'=>'C:\wamp\tmp\php30D6.tmp','name' => 'uploadify.swf', 'type' => 'application/x-shockwave-flash',
     * 'size' => 12767],[......],...]
     * @return array
     */
    public function addFiles($files)
    {
        $sep = DIRECTORY_SEPARATOR;
        $savepath = date('Y-m-d', time());
        $destpath = $this->vendor . $sep . $savepath;
        @mkdir($destpath, 0777, true);
        $added_files = [];
        foreach ($files as $key => $file) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $savename = $this->genUniqueStr() . '.' . $ext;
            $md5 = md5_file($file['file']);
            $sha1 = sha1_file($file['file']);
            if (($e_file = ModFile::find()->where(['md5' => $md5])->one()) == null) {
                if (is_uploaded_file($file['file'])) {
                    if (!move_uploaded_file($file['file'], $destpath . $sep . $savename)) continue;
                } else {
                    if (!is_file($file['file'])) continue;
                    if (false === copy($file['file'], $destpath . $sep . $savename)) continue;
                }
            } else {
                $savename = $e_file->savename;
                $savepath = $e_file->savepath;
                $md5 = $e_file->md5;
                $sha1 = $e_file->sha1;
            }
            $file_ar = new ModFile();
            $file['filename'] = $file['name'];
            $file['savename'] = $savename;
            $file['savepath'] = $savepath;
            $file['ext'] = $ext;
            $file['mime'] = $file['type'];
            $file['size'] = $file['size'];
            $file['md5'] = $md5;
            $file['sha1'] = $sha1;
            $file['location'] = '';
            $file['create_time'] = date('Y-m-d H:i:s', time());
            $file_ar->setAttributes($file);
            if (false === $file_ar->save()) continue;
            $added_files[$key] = $file_ar->getAttributes();

        }
        return $added_files;
    }

    public function genUniqueStr()
    {
        return md5(uniqid(md5(microtime(true)), true));
    }

    /**
     * @param array $file_ids
     * @return array|\service\base\type|\yii\db\ActiveRecord[]
     */
    public function getFiles(array $file_ids = [])
    {
        $where = array();
        if (!empty($file_ids)) $where['file_id'] = $file_ids;
        $files = ModFile::find()->where($where)->asArray()->all();
        if (false === $files) return $this->buildResponse('failed', 400, 'failed to get files');
        return $files;
    }

    /**
     * @inheritDoc
     */
    public function getFileFullPath(array $file)
    {
        return $this->getVendorPath() . DIRECTORY_SEPARATOR . $file['savepath'] . DIRECTORY_SEPARATOR . $file['savename'];
    }
}


