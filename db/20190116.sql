ALTER TABLE `tm_member_point_log`
ADD COLUMN `article_id`  int NULL DEFAULT 0 COMMENT '文章id' AFTER `member_id`,
ADD COLUMN `extend`  varchar(4000) NULL COMMENT '扩展字段' AFTER `article_id`;