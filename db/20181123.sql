ALTER TABLE `tm_client_version`
ADD COLUMN `is_upload` tinyint(1) NULL DEFAULT 0 COMMENT '是否上传文件' AFTER `user_id`;