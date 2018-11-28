ALTER TABLE `tm_start_adv`
MODIFY COLUMN `start_time` bigint(11) NULL DEFAULT 0 COMMENT '启用时间' AFTER `show_duration`;