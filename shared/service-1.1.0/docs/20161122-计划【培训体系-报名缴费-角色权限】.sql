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

alter table biz_order_goods comment '������Ʒ�� biz_order_goods';

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

alter table biz_refund comment '�˵��� biz_refund';

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

alter table biz_refund_goods comment '�˵���Ʒ�� biz_refund_goods';

/*==============================================================*/
/* Table: biz_refund_setting                                    */
/*==============================================================*/
create table biz_refund_setting
(
   id                   int not null,
   before_class_limit   int default -1 comment '��λ���룬-1��ʾ���������˿�',
   after_start_limit    int default -1 comment '-1���������˿1��δǩ��ʱ���������˿2��δ��ҵʱ���������˿�',
   review_mode          int default 1 comment '1���Զ����',
   amount               varchar(200),
   refund_mode          varchar(20) comment '1��ѧԱ΢���˻�
            2��ѧԱ΢ƽ̨���',
   times_limit          int comment '���ɹ��´ζ���ʹ�ã�����times_limit�β������������',
   primary key (id)
);

alter table biz_refund_setting comment '�˷����ñ� biz_refund_setting';

/*==============================================================*/
/* Table: pl_catalog                                            */
/*==============================================================*/
create table pl_catalog
(
   id                   int not null auto_increment,
   pid                  int not null default 0 comment 'Ϊ0��ʾ�޸�Ŀ¼�����Ǹ�Ŀ¼',
   name                 varchar(100) not null,
   status               int,
   xpath                varchar(1000),
   primary key (id)
);

alter table pl_catalog comment '��ѵ��ϵ';

/*==============================================================*/
/* Table: pl_enroll                                             */
/*==============================================================*/
create table pl_enroll
(
   id                   int not null auto_increment,
   plan_id              int not null,
   begin_time           datetime,
   end_time             datetime,
   limited              int comment '0��ʾ����',
   ischarge             int not null default 1,
   pay_mode             int comment '����֧��������֧��',
   pay_platform         varchar(200),
   cash_price           float,
   unit                 int comment 'ÿ�ˣ�ÿ��',
   time_limit           int comment '��λ��Сʱ',
   review_mode          int default 0 comment '0���Զ���ˣ�1���˹����',
   review_tips          varchar(1000),
   unsubscribe          varchar(10),
   unsubscribe_check    int,
   unsubscribe_tips     varchar(255),
   primary key (id)
);

alter table pl_enroll comment '�������ñ� pl_enroll';

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

alter table pl_permission comment '�ƻ�Ȩ�ޱ� pl_permission';

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

alter table pl_role comment '�ƻ���ɫ�� pl_role';

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

alter table pl_rs_plan_catalog comment '��ѵ-Ŀ¼ ��ϵ�� pl_rs_plan_catalog';

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

alter table pl_rs_role_permission comment '�ƻ��Ľ�ɫ-Ȩ�޹����� pl_rs_role_permission';

