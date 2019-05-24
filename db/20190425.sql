CREATE TABLE `cmqueue_task_info` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '任务自增ID',
  `task_name` varchar(255) NOT NULL DEFAULT '' COMMENT '任务别名',
  `task_queue` varchar(255) NOT NULL DEFAULT '' COMMENT '任务队列',
  `task_vhost` varchar(255) NOT NULL DEFAULT '' COMMENT '任务vhost',
  `task_param` varchar(10240) NOT NULL DEFAULT '' COMMENT '任务参数',
  `execute_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '执行方式',
  `duty` varchar(255) NOT NULL DEFAULT '' COMMENT '负责人',
  `alarm_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '告警方式',
  `mobiles` varchar(1024) NOT NULL DEFAULT '' COMMENT '手机号',
  `emails` varchar(1024) NOT NULL DEFAULT '' COMMENT '邮件',
  `req_url` varchar(512) NOT NULL DEFAULT '' COMMENT '请求URL',
  `rsp_param` varchar(1024) NOT NULL DEFAULT '' COMMENT '返回结果',
  `create_time` bigint(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` bigint(20) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '上下线状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='消息服务任务表';

CREATE TABLE `im_module_info` (
  `moduleaddr` varchar(32) NOT NULL DEFAULT '' COMMENT '模块地址',
  `modulename` varchar(16) NOT NULL DEFAULT '' COMMENT '模块名',
  `moduleversion` varchar(16) NOT NULL DEFAULT '' COMMENT '支持最低版本号',
  `balanceaddr` varchar(32) NOT NULL DEFAULT '' COMMENT '负载均衡地址',
  `connectcount` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户连接数',
  `uptime` bigint(20) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`moduleaddr`),
  UNIQUE KEY `moduleaddr` (`moduleaddr`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='登录服务记录表';

