<?php
/**
 *
 */

namespace service\file\interfaces;

/**
 * Interface File
 * 文件服务
 * 相关数据表file
 *
 * @package service\file\interfaces
 * @design yangzy 20161211
 * @author yangzy 20161211
 */
interface File
{
    /**
     * 返回文件存储地址
     *
     * @return string 文件存储地址
     */
    public function getVendorPath();

    /**
     * 获取文件地址的全路径
     *
     * @param array $file 文件信息 格式参照file表字段
     * @return string 成功时返回文件完整路径
     */
    public function getFileFullPath(array $file);
}