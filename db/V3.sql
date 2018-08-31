CREATE TABLE `tm_role_portal`  (
  `role_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '角色code',
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '能使用的Portal'
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

ALTER TABLE `tm_member_star`
MODIFY COLUMN `extend` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '扩展字段' AFTER `create_time`;

ALTER TABLE `tm_member_footprint`
MODIFY COLUMN `extend` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '扩展字段' AFTER `create_time`;

ALTER TABLE `tm_push_message`
ADD COLUMN `cid` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'cid极光推送标识' AFTER `status`,
ADD COLUMN `push_situation` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '推送情况' AFTER `cid`;

ALTER TABLE `tm_member` CHANGE `head_pic` `head_pic` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像';