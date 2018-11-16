CREATE TABLE `tm_member_third_party`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '第三方唯一标识',
  `type` int(255) NULL DEFAULT NULL COMMENT '1 QQ，2 Wechat，3 SinaWeibo',
  `add_time` bigint(11) NULL DEFAULT NULL COMMENT '添加时间',
  `device_model` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '第三方登录设备型号',
  `device_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '第三方登录设备类型 PC iOS Android',
  `login_time` bigint(11) NULL DEFAULT NULL COMMENT '登录时间',
  `ip` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '访问ip',
  `member_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '会员编号',
  `member_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '会员id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;


ALTER TABLE `tm_member`
ADD COLUMN `receive_notice` tinyint(1) NULL DEFAULT 1 COMMENT '接受消息 0不接受 1接受' AFTER `wb`,
ADD COLUMN `wifi_show_image` tinyint(1) NULL DEFAULT 0 COMMENT 'wifi下才显示图片 0否 1是' AFTER `receive_notice`,
ADD COLUMN `list_auto_play` tinyint(1) NULL DEFAULT 1 COMMENT '列表播放状态 0否 1是' AFTER `wifi_show_image`,
ADD COLUMN `ip` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'ip地址' AFTER `list_auto_play`;


CREATE TABLE `tm_system_article`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `article` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `content` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '内容',
  `update_time` bigint(11) NULL DEFAULT NULL COMMENT '更新时间',
  `add_time` bigint(11) NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

INSERT INTO `tm_system_article`(`id`, `article`, `content`, `update_time`, `add_time`) VALUES (1, '免责申明', '<p>123321</p>', 1534906369, 1534409002);
INSERT INTO `tm_system_article`(`id`, `article`, `content`, `update_time`, `add_time`) VALUES (2, '隐私协议', '<p>45645123321</p>', 1534906369, 1534409002);

CREATE TABLE `tm_member_opinion`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `message` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '反馈信息',
  `member_id` int(10) NOT NULL COMMENT '用户id',
  `add_time` bigint(11) NOT NULL COMMENT '添加时间',
  `status` tinyint(1) NULL DEFAULT NULL COMMENT '0关闭1正常2已处理',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

CREATE TABLE `tm_push_message`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标题',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '内容',
  `add_time` bigint(11) NULL DEFAULT NULL COMMENT '创建时间',
  `push_time` bigint(11) NULL DEFAULT NULL COMMENT '推送时间',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '链接',
  `status` int(255) UNSIGNED NULL DEFAULT 1 COMMENT '状态 0禁止 1正常 2已推送',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

ALTER TABLE `tm_member_star`
ADD COLUMN `tag` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '标记' AFTER `extend`;
ALTER TABLE `tm_member_star`
ADD COLUMN `type` tinyint(255) NULL DEFAULT 1 COMMENT '类型 1 文章 2视频' AFTER `tag`;

ALTER TABLE `tm_member_third_party`
ADD COLUMN `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '地区' AFTER `member_id`;

ALTER TABLE `tm_member_third_party`
ADD COLUMN `nick_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '昵称' AFTER `address`,
ADD COLUMN `head_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '头像链接' AFTER `nick_name`;

CREATE TABLE `tm_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '键名',
  `value` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '键值',
  `remarks` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注',
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '类型',
  `add_time` bigint(11) NULL DEFAULT NULL COMMENT '添加时间',
  `update_time` bigint(11) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

INSERT INTO `tm_config`(`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'site_name', '天马工场', '平台名称', 'base', 1534934035, 1535003056);
INSERT INTO `tm_config`(`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'site_logo', '/images/logo.png', '平台logo', 'base', 1534934035, 1535003056);
INSERT INTO `tm_config`( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'version', 'V1', '版本号', 'ios_version', 1535082506, 1535082506);
INSERT INTO `tm_config`( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'must_update', '1', '强制更新', 'ios_version', 1535082506, 1535082506);
INSERT INTO `tm_config`( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'version', 'V1', '版本号', 'android_version', 1535082566, 1535082566);
INSERT INTO `tm_config`( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'must_update', '1', '强制更新', 'android_version', 1535082566, 1535082566);
INSERT INTO `tm_config`( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'version', 'V2', '版本号', 'pc_version', 1535082566, 1535082566);
INSERT INTO `tm_config`( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'must_update', '1', '强制更新', 'pc_version', 1535082566, 1535082566);

CREATE TABLE `tm_member_footprint`  (
  `footprint_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '足迹主键',
  `member_code` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '足迹用户编码',
  `app_id` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'appid',
  `article_id` int(10) UNSIGNED NOT NULL COMMENT '文章id',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '文章标题',
  `intro` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '文章简介',
  `pic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '文章图片',
  `create_time` int(10) UNSIGNED NOT NULL COMMENT '收藏时间',
  `extend` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '扩展字段',
  `tag` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '标记',
  `type` tinyint(255) NULL DEFAULT 1 COMMENT '类型 1 文章 2视频',
  `status` tinyint(1) NULL DEFAULT 1 COMMENT '1正常0删除',
  PRIMARY KEY (`footprint_id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;


ALTER TABLE `tm_member`
MODIFY COLUMN `head_pic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像' AFTER `mobile`;

INSERT INTO `tm_privilege`(`privilege_id`, `privilege_type`, `privilege_name`, `privilege_code`, `privilege_intro`, `parent_pri_id`) VALUES (19, 0, '门户>门户图标', 'logoSet', '菜单权限', NULL);
INSERT INTO `tm_privilege`(`privilege_id`, `privilege_type`, `privilege_name`, `privilege_code`, `privilege_intro`, `parent_pri_id`) VALUES (20, 0, '门户>门户配置', 'ModuleConfig', '菜单权限', NULL);
INSERT INTO `tm_privilege`(`privilege_id`, `privilege_type`, `privilege_name`, `privilege_code`, `privilege_intro`, `parent_pri_id`) VALUES (21, 0, 'APP>APP配置', 'ComponentSet', '菜单权限', NULL);

INSERT INTO `tm_role_privilege`(`privilege_code`, `role_code`) VALUES ('mymember', '1');
INSERT INTO `tm_role_privilege`(`privilege_code`, `role_code`) VALUES ('logoSet', '1');
INSERT INTO `tm_role_privilege`(`privilege_code`, `role_code`) VALUES ('ModuleConfig', '1');
INSERT INTO `tm_role_privilege`(`privilege_code`, `role_code`) VALUES ('ComponentSet', '1');

INSERT INTO `tm_config`(`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('ali_sms_key_id', '', '阿里短信服务key', 'client', 1535446213, 1535446213);
INSERT INTO `tm_config`(`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('ali_sign_name', '', '阿里短信签名', 'client', 1535446213, 1535446213);
INSERT INTO `tm_config`(`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('ali_key_secret', '', '阿里短信服务secret', 'client', 1535446213, 1535446213);
INSERT INTO `tm_config`(`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('Jpush_key', '', '极光key', 'client', 1535446252, 1535446252);
INSERT INTO `tm_config`(`key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ('Jpush_secret', '', '极光secret', 'client', 1535446252, 1535446252);

