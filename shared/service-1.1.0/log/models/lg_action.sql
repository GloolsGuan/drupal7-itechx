/*
Navicat MySQL Data Transfer

Source Server         : www.dteols.cn
Source Server Version : 50629
Source Host           : www.dteols.cn:3306
Source Database       : dteols_dev

Target Server Type    : MYSQL
Target Server Version : 50629
File Encoding         : 65001

Date: 2016-12-19 19:36:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lg_action
-- ----------------------------
DROP TABLE IF EXISTS `lg_action`;
CREATE TABLE `lg_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `template` varchar(1000) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '-1：禁用，1：启用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='日志行为表 lg_action';

-- ----------------------------
-- Records of lg_action
-- ----------------------------
INSERT INTO `lg_action` VALUES ('1', 'VIEW_PLAN', '浏览计划', '[USER]查看了计划', '1');
INSERT INTO `lg_action` VALUES ('2', 'SIGN', '签到', '[USER]签到了日程', '1');
INSERT INTO `lg_action` VALUES ('3', 'DONE_SURVEY', '完成调研', '[USER]完成了调研', '1');
INSERT INTO `lg_action` VALUES ('4', 'CONFIRM_PLAN', '参加计划', '[USER]参加了计划', '1');
