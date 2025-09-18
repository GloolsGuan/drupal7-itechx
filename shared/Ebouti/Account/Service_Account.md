Database Schema
===============
```
    Ebouti 用户数据表，该数据表数据与微信保持一致，并在此基础上略有扩展。
```
create table eb_user(
    `id` int unsigned not null auto_increment primary key,
    `ma_customer_id` int not null default 0,
    `openid` char(32) not null default '',
    `nickname` varchar(50) not null default '',
    `mobile` varchar(32) not null default '',
    `standby_mobile` varchar(32) not null default '',
    `gender` TINYINT(1) not null default 0,
    `language` char(10) not null default '',
    `city` varchar(100) not null default '',
    `province` varchar(100) not null default '',
    `country` varchar(100) not null default '',
    `session_key` char(32) not null default '',
    `avatarurl` varchar(256) not null default '',
    `activated_app_wxapp` TINYINT(1) not null default 0,
    `activated_app_weixin` TINYINT(1) not null default 0,
    `activated_app_wxcorp` TINYINT(1) not null default 0,
    `activated_app_other1` char(30) not null default 0,
    `activated_app_other2` char(30) not null default 0,
    `activated_app_other3` char(30) not null default 0,
    `created_at` timestamp null,
    `updated_at` timestamp not null default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    index `openid` using hash (`openid`)
);

