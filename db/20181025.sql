

ALTER TABLE `tm_member`
ADD COLUMN `sex_edit_time`  int(11) NULL DEFAULT NULL COMMENT '性别上次编辑时间' AFTER `point`,
ADD COLUMN `birthday_edit_time`  int(11) NULL DEFAULT NULL COMMENT '生日上次编辑时间' AFTER `sex_edit_time`,
ADD COLUMN `mobile_edit_time`  int(11) NULL DEFAULT NULL COMMENT '电话上次编辑时间' AFTER `birthday_edit_time`,
ADD COLUMN `wb_edit_time`  int(11) NULL DEFAULT NULL COMMENT '微博上次编辑时间' AFTER `mobile_edit_time`,
ADD COLUMN `wx_edit_time`  int(11) NULL DEFAULT NULL COMMENT '微信上次编辑时间' AFTER `wb_edit_time`,
ADD COLUMN `qq_edit_time`  int(11) NULL DEFAULT NULL COMMENT 'qq上次编辑时间' AFTER `wx_edit_time`,
ADD COLUMN `sign_num`  int(11) NULL DEFAULT NULL COMMENT '连续签到次数' AFTER `qq_edit_time`,
ADD COLUMN `sign_time`  int(11) NULL DEFAULT NULL COMMENT '上次签到时间' AFTER `sign_num`,
ADD COLUMN `close_start_time`  int NULL DEFAULT 0 COMMENT '封号开始时间' AFTER `sign_time`,
ADD COLUMN `close_end_time`  int NULL DEFAULT 0 COMMENT '封号结束时间' AFTER `close_start_time`,
ADD COLUMN `close_down_point`  int NULL DEFAULT 0 COMMENT '上次封号扣除积分' AFTER `close_end_time`,
ADD COLUMN `close_reason`  varchar(255) NULL COMMENT '上次封号原因' AFTER `close_down_point`,
ADD COLUMN `login_type`  varchar(10) NULL COMMENT '登录类型' AFTER `close_reason`;

INSERT INTO `tm_system_article` (`id`, `article`, `content`, `update_time`, `add_time`) VALUES ('4', '1', '积分规则', '1539757706', NULL);

INSERT INTO `tm_config` (`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'first_login', '20', NULL, 'point', '1539754645', '1539754645');
INSERT INTO `tm_config` (`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('sex', '20', NULL, 'point', '1539754701', '1539754701');
INSERT INTO `tm_config` (`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('birthday', '20', NULL, 'point', '1539754710', '1539754710');
INSERT INTO `tm_config` (`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('mobile', '20', NULL, 'point', '1539754720', '1539754720');
INSERT INTO `tm_config` (`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'wb', '20', NULL, 'point', '1539754728', '1539754728');
INSERT INTO `tm_config` ( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'wx', '20', NULL, 'point', '1539754737', '1539754737');
INSERT INTO `tm_config` ( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'qq', '20', NULL, 'point', '1539754745', '1539754745');
INSERT INTO `tm_config` ( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('sign', '20', NULL, 'point', '1539754818', '1539754818');
INSERT INTO `tm_config` ( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('sign_cycle_first', '10', '3', 'point', '1539755515', '1539755515');
INSERT INTO `tm_config` (`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('sign_cycle_two', '20', '7', 'point', '1539755548', '1539755548');
INSERT INTO `tm_config` ( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('sign_extra_two', '400', '200', 'point', '1539755608', '1539755608');
INSERT INTO `tm_config` ( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('sign_extra_first', '200', '100', 'point', '1539755622', '1539755622');
INSERT INTO `tm_config` (`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'first_login_switch', '1', NULL, 'point', '1539755926', '1539755926');
INSERT INTO `tm_config` (`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('perfect_information_switch', '1', NULL, 'point', '1539755992', '1539755992');
INSERT INTO `tm_config` (`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('sign_switch', '1', NULL, 'point', '1539756013', '1539756013');


CREATE TABLE `tm_user_behavior_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户行为log',
  `content` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `member_id` int(11) DEFAULT NULL COMMENT '会员id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

CREATE TABLE `tm_adv`  (
  `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片链接',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '调整链接',
  `sort` int(8) NULL DEFAULT NULL COMMENT '排序',
  `is_login_skip` int(1) NULL DEFAULT 0 COMMENT '是否登录显示',
  `status` int(1) NULL DEFAULT 1 COMMENT '0删除1正常',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

INSERT INTO `tm_adv`(`image`, `url`, `sort`, `is_login_skip`, `status`) VALUES ('/images/banner.png','', 5, 1, 1);
INSERT INTO `tm_config`(`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('BackGroupPic', '/images/backgroup.png', '个人中心背景图', 'client', 1541059566, 1541059566);

ALTER TABLE `tm_adv`
ADD COLUMN `form` int(1) NULL DEFAULT 0 COMMENT '类型 0为非原生 1为原生' AFTER `status`,
ADD COLUMN `ios_info` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'ios跳转信息' AFTER `form`,
ADD COLUMN `android_info` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'android跳转信息' AFTER `ios_info`,
ADD COLUMN `unit_id` int(8) NULL DEFAULT NULL COMMENT '组件id' AFTER `android_info`,
ADD COLUMN `unit_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '组件名称' AFTER `unit_id`;

ALTER TABLE `tm_member`
ADD COLUMN `member_sn` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '用户sn账号' AFTER `login_type`;

update tm_member set member_sn = CONCAT('cshm',999999 - member_id) where member_sn is NULL;



CREATE TABLE `tm_common_article` (
`article_id`  int NOT NULL AUTO_INCREMENT COMMENT '爬取的新闻数据表主键' ,
`aid`  varchar(100) NULL COMMENT '文章id，不会重复' ,
`website_name`  varchar(100) NULL COMMENT '爬取的网站名称' ,
`title`  varchar(255) NULL COMMENT '文章标题' ,
`content`  longtext NULL COMMENT '文章内容' ,
`keyword`  varchar(500) NULL COMMENT '关键字' ,
`abstract`  varchar(1000) NULL COMMENT '简介' ,
`from_source`  varchar(255) NULL COMMENT '来源' ,
`from_source_url`  varchar(500) NULL COMMENT '来源url' ,
`url`  varchar(500) NULL COMMENT '原文章url' ,
`author`  varchar(100) NULL COMMENT '作者' ,
`organization`  varchar(255) NULL COMMENT '发布机构' ,
`column`  varchar(50) NULL COMMENT '所属栏目' ,
`publish_time`  datetime NULL COMMENT '发布时间' ,
`comment_num`  varchar(10) NULL COMMENT '评论数' ,
`read_num`  varchar(10) NULL COMMENT '阅读数' ,
`collect_time`  datetime NULL COMMENT '采集时间' ,
`img_url1`  varchar(255) NULL COMMENT '缩略图1' ,
`img_url2`  varchar(255) NULL COMMENT '缩略图2' ,
`img_url3`  varchar(255) NULL COMMENT 'img_url3' ,
`column_id`  int NULL COMMENT '栏目id' ,
`from_id`  int NULL COMMENT '来源id' ,
`put_time`  datetime NULL COMMENT '入库时间' ,
PRIMARY KEY (`article_id`)
)
;

CREATE TABLE `tm_common_article_aids` (
`id`  int NOT NULL AUTO_INCREMENT COMMENT '爬取的新闻数据aid表主键' ,
`aids`  longtext NULL COMMENT '还未处理的文章aid集合' ,
`create_time`  datetime NULL COMMENT '创建时间' ,
`update_time`  datetime NULL COMMENT '更新时间' ,
`is_stop`  tinyint NULL DEFAULT 0 COMMENT '是否停止更新数据，0否，1停止',
PRIMARY KEY (`id`)
);

INSERT INTO `tm_config`(`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('auto_location', '1', '是否自动获取地址', 'client', 1536292655, 1540974064);


ALTER TABLE `tm_component`
ADD COLUMN `sql_version` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '数据表版本' AFTER `android_info`;

CREATE TABLE `tm_adv`  (
  `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片链接',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '调整链接',
  `sort` int(8) NULL DEFAULT NULL COMMENT '排序',
  `is_login_skip` int(1) NULL DEFAULT 0 COMMENT '是否登录跳转',
  `status` int(1) NULL DEFAULT 1 COMMENT '0删除1正常',
  `form` int(1) NULL DEFAULT 0 COMMENT '类型 0为非原生 1为原生',
  `ios_info` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'ios跳转信息',
  `android_info` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'android跳转信息',
  `unit_id` int(8) NULL DEFAULT NULL COMMENT '组件id',
  `unit_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '组件名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

INSERT INTO `tm_adv`(`image`, `url`, `sort`, `is_login_skip`, `status`, `form`, `ios_info`, `android_info`, `unit_id`, `unit_name`) VALUES ('/images/banner.png', '', 5, 1, 1, 0, NULL, NULL, NULL, NULL);

ALTER TABLE tm_member`
ADD COLUMN `member_sn` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户sn账号' AFTER `login_type`;

ALTER TABLE `tm_adv`
ADD COLUMN `add_time` bigint(11) NULL COMMENT '新增时间' AFTER `unit_name`,
ADD COLUMN `update_time` bigint(11) NULL COMMENT '更新时间' AFTER `add_time`;