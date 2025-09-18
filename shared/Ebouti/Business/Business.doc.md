## 设计思路 ##
业务服务主要涉及到资源管理与业务管理两个部分，是实际的商业交互的核心部分，产品|服务／交易。
期完整的设计功能包括：
1. 资源管理。
2. 订单管理。
3. 营销管理。
三个部分，所谓营销是基于条件生成的产品销售策略，订单基于该策略创建销售的业务实例，并生成正式的交易订单。

#### 服务与资源 ####
商品是资源，商品销售是服务。


```
CREATE TABLE `resource_biz` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bundle_name` varchar(100) NOT NULL DEFAULT '',
  `unique_code` char(32) NOT NULL DEFAULT '',
  `owner` varchar(150) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `udpated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `source_status` enum('activity','locked','removed') DEFAULT 'activity',
  `xpath` varchar(150) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `customer_id` int unsigned not null default 0,
  `store_id` int unsigned not null default 0,
  `creator_id` int unsigned not null default 0,
  `base_price` decimal(8,3) not null default '0.000',
  `released_at` timestamp ,
  `expired_at`  timestamp ,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_code` (`bundle_name`,`unique_code`),
  KEY `xpath` (`xpath`)
);
```