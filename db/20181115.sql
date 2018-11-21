ALTER TABLE `tm_client_version` 
ADD COLUMN `user_id` int(0) NULL COMMENT '操作管理员主键' AFTER `client_type`;