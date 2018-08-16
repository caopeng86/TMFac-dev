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


