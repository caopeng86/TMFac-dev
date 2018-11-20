ALTER TABLE `tm_member`
ADD COLUMN `point` int(11) NULL DEFAULT 0 COMMENT '用户积分' AFTER `ip`;

CREATE TABLE `tm_member_point_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `change_point` int(11) NULL DEFAULT 0 COMMENT '变动积分',
  `now_point` int(11) NULL DEFAULT 0 COMMENT '当时的积分',
  `remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '备注信息',
  `from_component` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '本次积分变动从哪个组件出发',
  `add_time` bigint(11) NULL DEFAULT NULL COMMENT '添加时间',
  `member_id` int(11) NULL DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

