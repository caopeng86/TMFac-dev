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

INSERT INTO `tm_system_article`(`article`, `content`, `update_time`, `add_time`) VALUES ('关于我们', '<p>45645123321</p>', 1534906369, 1534409002);

ALTER TABLE `tm_push_message`
ADD COLUMN `ios_info` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'ios推送参数' AFTER `push_situation`,
ADD COLUMN `android_info` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'android推送参数' AFTER `ios_info`,
ADD COLUMN `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '推送类型' AFTER `android_info`;

ALTER TABLE `tm_push_message`
MODIFY COLUMN `push_situation` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '推送情况' AFTER `cid`;
UPDATE tm_push_message SET ios_info = '{"native":true,"src":"SetI001MessageController","paramStr":"","wwwFolder":""}',android_info='{"native":true,"src":"com.tenma.ventures.usercenter.view.PcWeiduNewActivity","paramStr":"","wwwFolder":""}',type='系统消息';

CREATE TABLE `tm_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tm_config`( `key`, `value`, `remarks`, `type`, `add_time`, `update_time`) VALUES ( 'ali_check_template_code', '321211', '阿里验证短信模板code', 'client', 1536215833, 1536215833);

ALTER TABLE `tm_component`
ADD COLUMN `ios_info` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'ios入口' AFTER `component_pic`,
ADD COLUMN `android_info` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'android入口' AFTER `ios_info`;

INSERT INTO `tm_privilege`(`privilege_type`, `privilege_name`, `privilege_code`, `privilege_intro`, `parent_pri_id`) VALUES (0, '门户>门户图标', 'logoSet', '菜单权限', NULL);
INSERT INTO `tm_privilege`(`privilege_type`, `privilege_name`, `privilege_code`, `privilege_intro`, `parent_pri_id`) VALUES (0, '门户>门户配置', 'ModuleConfig', '菜单权限', NULL);
INSERT INTO `tm_privilege`(`privilege_type`, `privilege_name`, `privilege_code`, `privilege_intro`, `parent_pri_id`) VALUES (0, 'APP>APP配置', 'ComponentSet', '菜单权限', NULL);
INSERT INTO `tm_privilege`(`privilege_type`, `privilege_name`, `privilege_code`, `privilege_intro`, `parent_pri_id`) VALUES (0, '账户>账户信息', 'mymember', '菜单权限', NULL);

INSERT INTO `tm_role_privilege`(`privilege_code`, `role_code`) VALUES ('mymember', '1');
INSERT INTO `tm_role_privilege`(`privilege_code`, `role_code`) VALUES ('logoSet', '1');
INSERT INTO `tm_role_privilege`(`privilege_code`, `role_code`) VALUES ('ModuleConfig', '1');
INSERT INTO `tm_role_privilege`(`privilege_code`, `role_code`) VALUES ('ComponentSet', '1');
