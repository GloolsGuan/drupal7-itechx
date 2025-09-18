--
-- Author: GloolsGuan
-- 课程数据表支持官方和社区课程所属，如社区课程可以所属于一个群组或某个人。
-- 同时社区课程的内容直接就在课程表里，属于简单课程模式，不支持课程sessio机制。
-- 机构课程属于专业体系，课程表内容部分会成为课程规划说明，课程具体内容将以session
-- 模式独立创作。
-- 课程社区和tags支持都使用service体系提供的公共社区和tags支持，所以这里不特别说明。
DROP TABLE IF EXISTS `course`;
CREATE TABLE `course`(
    `id` int unsigned not null auto_increment primary key,
    `title` varchar(255) not null default '',
    `course_code` char(64) not null default '',
    `creator_code` char(64) not null default '',
    `brief` varchar(255) not null default '',
    `group_id` int not null default 0,
    `publish_type` enum('official', 'community') not null default 'community',
    `status` enum('inactive','actived','locked','expired','removed') not null default 'inactive',
    `created_at` int(11) not null default 0,
    `updated_at` int(11) not null default 0,
    `content_render` varchar(255) not null default '',
    `content` text
);

DROP TABLE IF EXISTS `course_logs`;
CREATE TABLE `course_logs`(
    `id` int unsigned not null auto_increment primary key,
    `operator_code` char(64) not null default '',
    `created_at` int(11) not null default 0,
    `user_agent_info` text,
    `log` text
);



DROP TABLE IF EXISTS `course_session`;
CREATE TABLE `course_session`(
    `id` int unsigned not null auto_increment primary key,
    `title` varchar(255) not null default '',
    `creator_code` char(64) not null default '',
    `session_time` int(3) not null default 0 comment "Session time, minite",
    `status` enum('inactive','actived','locked','expired','removed') not null default 'inactive',
    `session_type` varchar(200) not null default '' comment "allowed values:normal, exam, survey, activity, task, discuss, meet and so on",
    `created_at` int(11) not null default 0,
    `updated_at` int(11) not null default 0
);

DROP TABLE IF EXISTS `course_session_content`;
CREATE TABLE `course_session_content`(
    `id` int unsigned not null auto_increment primary key,
    `session_id` int not null default 0,
    `brief` varchar(255) not null default 0,
    `type` varchar(100) not null default 0,
    `content_render` varchar(100) not null default '',
    `content` text
);

DROP TABLE IF EXISTS `course_teacher`;
CREATE TABLE `course_teacher`(
    `id` int unsigned not null auto_increment primary key,
    `course_id` int not null default 0,
    `session_id` int not null default 0,
    `user_code` char(64) not null default '',
    `role_type` enum('teacher', 'assistant') not null default 'teacher',
    `status` enum('active', 'locked', 'removed') not null default 'active',
    `note` text,
    UNIQUE KEY (`course_id`,`session_id`,`user_code`)
);
