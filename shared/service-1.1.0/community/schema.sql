DROP TABLE IF EXISTS ``;
CREATE TABLE ``(
    `id` int unsigned not null auto_increment primary key,
    `member_id` int unsigned not null default 0,
    `created_at` int(11) not null default 0,
    `updated_at` int(11) not null default 0,
);

DROP TABLE IF EXISTS `community`;
CREATE TABLE `community`(
    `id` int unsigned not null auto_increment primary key,
    `title` varchar(255) not null default '',
    `master_title` varchar(255) not null default '',
    `master_code` char(64) not null default '',
    `master_ext_id` int unsigned not null default 0,
    `founder_uc_code` char(64) not null default '',
    `operator_uc_code` char(64) not null default '',
    `created_at` int(11) not null default 0,
    `updated_at` int(11) not null default 0,
    `status` enum('inactive','active','locked','removed') not null default 'active',
    `logo` varchar(255) not null default '',
    `settings` text,
    `intro` text,
    UNIQUE KEY (`master_code`,`master_ext_id`)
);

DROP TABLE IF EXISTS `community_group`;
CREATE TABLE `community_group`(
    `id` int unsigned not null auto_increment primary key,
    `community_id` int unsigned not null default 0,
    `parent_id` int unsigned not null default 0,
    `title` varchar(255) not null default '',
    `type` varchar(100) not null default '',
    `creator_uc_code` char(64) not null default '',
    `created_at` int(11) not null default 0,
    `updated_at` int(11) not null default 0,
    `logo` varchar(255) not null default '',
    `x_path` varchar(255) not null default '',
    `operator_uc_code` char(64) not null default '',
    `status` enum('inactive','active','locked','removed') not null default 'active',
    `settings` text,
    `intro` text
);

DROP TABLE IF EXISTS `community_group_member`;
CREATE TABLE `community_group_member`(
    `id` int unsigned not null auto_increment primary key,
    `group_id` int unsigned not null default 0,
    `uc_user_code` char(64) not null default '',
    `role_id` int unsigned not null default 0,
    `level_id` int unsigned not null default 0,
    `participation_type` enum('take_part_in', 'following', 'founder') not null default 'take_part_in' comment "参与类型",
    `honor_title` varchar(255) not null default '' comment "荣誉头衔",
    `nickname` varchar(255) not null default 0,
    `status` enum('inactive','active','locked','removed') not null default 'inactive',
    `created_at` int(11) not null default 0,
    `updated_at` int(11) not null default 0,
    `settings` text,
    UNIQUE KEY (`group_id`,`uc_user_code`)
);




DROP TABLE IF EXISTS `community_topic`;
CREATE TABLE `community_topic`(
    `id` int unsigned not null auto_increment primary key,
    `group_id` int unsigned not null default 0,
    `parent_id` int unsigned not null default 0,
    `member_id` int unsigned not null default 0,
    `member_uc_code` char(64) not null default '',
    `category_id` int unsigned not null default 0,
    `title` varchar(255) not null default '',
    `created_at` int(11) not null default 0,
    `updated_at` int(11) not null default 0,
    `content_render_format` varchar(100) not null default '',
    `x_path` varchar(255) not null default '',
    `content` text
);

DROP TABLE IF EXISTS `community_like`;
CREATE TABLE `community_like`(
    `id` int unsigned not null auto_increment primary key,
    `member_id` int unsigned not null default 0,
    `member_uc_code` char(64) not null default '',
    `like_type` enum('thumbs_up', 'collecting') not null default 'collecting',
    `like_object` varchar(100) not null default '',
    `object_id` int not null default 0,
    `created_at` int(11) not null default 0,
    `updated_at` int(11) not null default 0
);