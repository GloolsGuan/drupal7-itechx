/*
    Community 数据库结构设计
*/

--- form event ---
---
    表单事件依赖于有效的用户，目的在于保持多表单数据提交的统一性。
    表单在未完成状态下，会在系统缓存，在过期时间之前，都可以提取
    该表单事件管辖的所有数据，并重新创建表单编辑状态。
    表单事件需要与表单数据表配合使用。
---
create table la_form_event(
    `eid` int unsigned not null comment "Event ID",
    `uid` int unsigned not null comment "User Id, The user must be a authenticated user.",
    `create_at` timestamp default CURRENT_TIMESTAMP,
    `expire` timestamp default 0,
    `status` int default 1 comment "Lock: 0, Normal:1", 
    `form_ids` text not null default ''
);

create table form_event_cache(
    `eid` int not null comment "Event ID",
    `form_id` varchar(100) not null default '',
    `form_data` text
);

--- community ---
---
    社区，基本数据表
---
create table la_community(
    `cid` int unsigned not null comment "Community ID",
    `creator_id` int unsigned not null default 0,
    `intro` varchar(255),
    `level` int not null default 0,
    `growthofintegral` int not null default 0,
    `logo` int not null default 0,
    `create_at` timestamp default 0,
    `update_at` timestamp default CURRENT_TIMESTAMP
);

--- community_activity ---
---
    社区活动数据表
---
create table la_community_activity(
    `caid` int unsigned not null comment "Community activity ID",
    `cid`  int unsigned not null comment "Community ID",
    `uid`  int unsigned not null comment "User ID",
    `title` varchar(250) not null default '',
    `logo` int not null default 0,
    `tags` varchar(200) not null default 0,
    `city` int not null default 0,
    `fee`  int unsigned not null default 0,
    `address` varchar(255) not null default 0,
    `activity_date` timestamp default 0,
    `status`  enum('lock', 'draf', 'normal', 'expire'),
    `detail` text
);


---

--- community_members ---
---
    该数据表说明某一个社区内具体的会员状况。
    活动社区会员,该数据表为暂时定义，稍后还会添加数据段。
---
create table community_members(
    `cid` int not null default 0 comment "Community ID",
    `caid` int not null default 0 comment "Community activity ID",
    `uid` int not null default 0 comment "Who invite you",
    `invited_id` int not null default 0 "Who was invited?",
    
);

--- lasooo_members ---
---
    扩展drupal内核会员数据表
---
create table lasooo_members(
    `level` int not null default 0,
    `goldcoin` int not null default 0,
    `weibo` varchar(50) not null default "",
    `weixin` varchar(50) not null default "",
    `mobile` varchar(20) not null default ""
);

