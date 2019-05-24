ALTER TABLE `tm_member`
ADD COLUMN `jpush_id`  varchar(100) NULL DEFAULT NULL COMMENT '激光用户id' AFTER `channel_sources`;



