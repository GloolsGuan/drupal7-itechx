
create table xplan_target(
    `id` int unsigned not null auto_increment primary key,
    `title` varchar(255) not null default '',
    `short_title` varchar(30) not null default '',
    `schema` char(50) not null default '',
    `creator_id` char(64) not null default 0,
    `logo` varchar(255) not null default '',
    `visibility` enum('public', 'protected', 'private') not null default 'private',
    `status` enum('editing', 'actived', 'expired', 'canceled', 'removed') not null default 'editing',
    `created_at` int(11) not null default 0,
    `updated_at` int(11) not null default 0,
    `intro` text,
    `settings` text
);


create table xplan_members(
    `id` int unsigned not null auto_increment primary key,
    `target_id` int not  null default 0,
    `user_code` char(64) not null default '' comment "用户中心唯一用户code",
    `contact_mobile` varchar(20) not null default '' comment "参与该次计划的联系方式",
    `organization_name` varchar(255) not null default 0 comment "参与此次会议的所属企业或机构名称",
    `organization_position` varchar(200) not null default 0 comment "参与此次计划声明的所在机构职位",
    `call_title` varchar(200) not null default '' comment "与会称呼",
    `participating_raw_code` char(64) not null default '' comment "Used for build QR code.", 
    `participating_type` enum('invited', 'apply', 'open') not null default '' comment "参与类型，包含被邀请，申请，和开放报名",
    `guest_level` int(1) not null default 0 comment "The normal level is 0, The VIP level is 9",
    `is_synctowx_ok` enum('yes', 'no','waiting') comment "是否同步到微信",
    `comfirm_status` enum('participating', 'following', 'refused', 'waiting') not null default 'waiting' comment "参与计划确认状态，可以是参与、关注、拒绝或等待",
    `confirmed_at` int(11) not null default 0 comment "确认时间",
    `created_at` int(11) not null default 0,
    `updated_at` int(11) not null default 0
);


create table xplan_rs_member_role(
    `member_id` int not null default 0,
    `role_id` int not null default 0,
    `operator_id` int not null default 0,
    `created_at` int(11) not null default 0,
    `updated_at` int(11) not null default 0
);


create table xplan_roles(
    `id` int unsigned not null auto_increment primary key,
    `title` varchar(100) not null default '',
    `desc` varchar(255) not null default ''
);


create table xplan_rs_role_permission(
    `role_id` int not null default 0,
    `permission_id` int not null default 0
);

create table xplan_permissions(
    `id` int unsigned not null auto_increment primary key,
    `title` varchar(100) not null default '',
    `desc` varchar(255) not null default '',
    `filters` text
);


create table xplan_schedule(
    `id` int unsigned not null auto_increment primary key,
    `title` varchar(255) not null default '',
    `rs_id` int not null default 0,
    `owner_code` char(32) not null default '' comment "Who is the owner of schedule, The value will be 16bit string.",
    `created_at` int(11) not null default '',
    `settings` text,
    `intro` text
);


create table xplan_schedule_items(
    `id` int unsigned not null auto_increment primary key,
    `schedule_id` int not null default 0,
    `title` varchar(200) not null default '',
    `start_from` int(11) not null default '',
    `end_at` int(11) not null default '', 
    `created_at` int(11) not null default '',
    `updated_at` int(11) not null default '',
    `intro` text 
);