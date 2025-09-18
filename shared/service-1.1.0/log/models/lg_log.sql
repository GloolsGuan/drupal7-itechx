/*
Navicat MySQL Data Transfer

Source Server         : www.dteols.cn
Source Server Version : 50629
Source Host           : www.dteols.cn:3306
Source Database       : dteols_dev

Target Server Type    : MYSQL
Target Server Version : 50629
File Encoding         : 65001

Date: 2016-12-19 19:36:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lg_log
-- ----------------------------
DROP TABLE IF EXISTS `lg_log`;
CREATE TABLE `lg_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unique_code` varchar(100) NOT NULL,
  `action_id` int(11) NOT NULL,
  `create_time` datetime NOT NULL,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='日志记录表 lg_log';
