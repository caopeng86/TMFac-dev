ALTER TABLE `tm_member_star`
MODIFY COLUMN `extend`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '扩展字段哦' AFTER `create_time`;

