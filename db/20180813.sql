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

CREATE TABLE `tm_member_opinion`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `message` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '反馈信息',
  `member_id` int(10) NOT NULL COMMENT '用户id',
  `add_time` bigint(11) NOT NULL COMMENT '添加时间',
  `status` tinyint(1) NULL DEFAULT NULL COMMENT '0关闭1正常2已处理',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

CREATE TABLE `tm_push_message`  (
  `id` int(11) NOT NULL COMMENT '主键',
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


