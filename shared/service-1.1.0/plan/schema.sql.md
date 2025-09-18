### 

create table rs_plan_user(
  `plan_id` int not null default 0,
  `user_id` int not null default 0,
  `role_id` varchar(20) not null default '',
  `status` enum('waiting', 'active', 'locked', 'removed') not null default 'waiting',
  `operator` int not null default 0 comment "The user_id who operating the record",
  `created_at` int(11) not null default 0 comment "UNIQUE_TIMESTAMP(NOW())",
  `updated_at` int(11) not null default 0 comment "UNIQUE_TIMESTAMP(NOW())",
  UNIQUE INDEX (`plan_id`, `user_id`, `role_id`)
);