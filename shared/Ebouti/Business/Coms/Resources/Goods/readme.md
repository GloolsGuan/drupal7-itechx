```
create table resource_biz_goods(
    `resource_id` int unsigned not null default 0,
    `designer` varchar(150) not null default '',
    `designer_avatar` varchar(255) not null default '',
    `design_link` varchar(255) not null default '',
    `sku` varchar(100) not null default '',
    `unit` varchar(100) not null default '',
    `quality` int unsigned not null default 0,
    `stocks` int unsigned not null default 0,
    `origin` varchar(100) not null default 0,
    `bundle_status` enum('waiting','selling','stockout','soldout','locked') not null default 'waiting',
    `bundle_status_note` varchar(255) not null default '',
    `design_note` text,
    UNIQUE INDEX `resource_id`(`resource_id`),
    UNIQUE INDEX `sku`(`sku`)
);
```