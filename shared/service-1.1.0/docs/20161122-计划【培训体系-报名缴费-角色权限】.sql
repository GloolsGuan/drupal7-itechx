/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2016/11/22 15:45:34                          */
/*==============================================================*/


drop table if exists biz_order_goods;

drop table if exists biz_refund;

drop table if exists biz_refund_goods;

drop table if exists biz_refund_setting;

drop table if exists pl_catalog;

drop table if exists pl_enroll;

drop table if exists pl_permission;

drop table if exists pl_role;

drop table if exists pl_rs_plan_catalog;

drop table if exists pl_rs_role_permission;

/*==============================================================*/
/* Table: biz_order_goods                                       */
/*==============================================================*/
create table biz_order_goods
(
   id                   int not null auto_increment,
   order_id             int not null,
   good_number          int not null,
   course_id            int not null,
   course_name          varchar(100),
   course_desc          varchar(0),
   course_begin_time    datetime,
   course_end_time      datetime,
   course_type          int,
   course_detail        text,
   course_address       varchar(500),
   course_pay_setting   text not null,
   primary key (id)
);

alter table biz_order_goods comment '订单商品表 biz_order_goods';

/*==============================================================*/
/* Table: biz_refund                                            */
/*==============================================================*/
create table biz_refund
(
   id                   int not null auto_increment,
   order_id             varchar(200) not null,
   unique_code          varchar(200) not null,
   course_id            int not null,
   refund_reason        varchar(1000),
   create_time          datetime,
   status               int,
   reviewer             varchar(200),
   review_time          datetime,
   reason               varchar(1000),
   primary key (id)
);

alter table biz_refund comment '退单表 biz_refund';

/*==============================================================*/
/* Table: biz_refund_goods                                      */
/*==============================================================*/
create table biz_refund_goods
(
   id                   int not null auto_increment,
   refund_id            int not null,
   course_id            int,
   primary key (id)
);

alter table biz_refund_goods comment '退单商品表 biz_refund_goods';

/*==============================================================*/
/* Table: biz_refund_setting                                    */
/*==============================================================*/
create table biz_refund_setting
(
   id                   int not null,
   before_class_limit   int default -1 comment '单位：秒，-1表示可以申请退款',
   after_start_limit    int default -1 comment '-1：不可以退款，1：未签到时可以申请退款，2：未结业时可以申请退款',
   review_mode          int default 1 comment '1：自动审核',
   amount               varchar(200),
   refund_mode          varchar(20) comment '1：学员微信账户
            2：学员微平台余额',
   times_limit          int comment '余额可供下次订课使用，超过times_limit次不用则余额作废',
   primary key (id)
);

alter table biz_refund_setting comment '退费设置表 biz_refund_setting';

/*==============================================================*/
/* Table: pl_catalog                                            */
/*==============================================================*/
create table pl_catalog
(
   id                   int not null auto_increment,
   pid                  int not null default 0 comment '为0表示无父目录，即是根目录',
   name                 varchar(100) not null,
   status               int,
   xpath                varchar(1000),
   primary key (id)
);

alter table pl_catalog comment '培训体系';

/*==============================================================*/
/* Table: pl_enroll                                             */
/*==============================================================*/
create table pl_enroll
(
   id                   int not null auto_increment,
   plan_id              int not null,
   begin_time           datetime,
   end_time             datetime,
   limited              int comment '0表示不限',
   ischarge             int not null default 1,
   pay_mode             int comment '在线支付、积分支付',
   pay_platform         varchar(200),
   cash_price           float,
   unit                 int comment '每人，每次',
   time_limit           int comment '单位：小时',
   review_mode          int default 0 comment '0：自动审核，1：人工审核',
   review_tips          varchar(1000),
   unsubscribe          varchar(10),
   unsubscribe_check    int,
   unsubscribe_tips     varchar(255),
   primary key (id)
);

alter table pl_enroll comment '报名配置表 pl_enroll';

/*==============================================================*/
/* Table: pl_permission                                         */
/*==============================================================*/
create table pl_permission
(
   id                   int not null auto_increment,
   title                varchar(20) not null,
   path                 varchar(200) not null,
   `desc`               varchar(200),
   primary key (id)
);

alter table pl_permission comment '计划权限表 pl_permission';

/*==============================================================*/
/* Table: pl_role                                               */
/*==============================================================*/
create table pl_role
(
   id                   int not null auto_increment,
   name                 varchar(20) not null,
   `desc`               varchar(200),
   primary key (id)
);

alter table pl_role comment '计划角色表 pl_role';

/*==============================================================*/
/* Table: pl_rs_plan_catalog                                    */
/*==============================================================*/
create table pl_rs_plan_catalog
(
   id                   int not null auto_increment,
   catalog_id           int not null,
   plan_id              int not null,
   primary key (id)
);

alter table pl_rs_plan_catalog comment '培训-目录 关系表 pl_rs_plan_catalog';

/*==============================================================*/
/* Table: pl_rs_role_permission                                 */
/*==============================================================*/
create table pl_rs_role_permission
(
   id                   int not null auto_increment,
   role_id              int not null,
   permission_id        int not null,
   primary key (id)
);

alter table pl_rs_role_permission comment '计划的角色-权限关联表 pl_rs_role_permission';

