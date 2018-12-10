ALTER TABLE `tm_user`
ADD COLUMN `origin` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '来源' AFTER `extend`;