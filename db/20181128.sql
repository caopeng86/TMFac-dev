ALTER TABLE `tm_member` 
ADD COLUMN `register_source` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'APP' COMMENT '注册来源' AFTER `member_sn`;