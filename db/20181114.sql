ALTER TABLE `tm_config`
MODIFY COLUMN `value`  varchar(5000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '键值' AFTER `key`;
CREATE TABLE `tm_client_version`  (
  `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `version` bigint(11) NULL DEFAULT NULL COMMENT '版本号',
  `add_time` bigint(11) NULL DEFAULT NULL COMMENT '添加时间',
  `is_force` tinyint(1) NULL DEFAULT 0 COMMENT '是否强制更新 1强制 0 否',
  `update_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '更新链接',
  `display_version` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '展示版本号',
  `remarks` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '版本号备注信息',
  `client_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'iOS' COMMENT '类型 iOS Android',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;