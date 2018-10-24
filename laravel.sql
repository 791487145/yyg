/*
 Navicat Premium Data Transfer

 Source Server         : 120.27.125.247
 Source Server Type    : MySQL
 Source Server Version : 50547
 Source Host           : 120.27.125.247
 Source Database       : laravel

 Target Server Type    : MySQL
 Target Server Version : 50547
 File Encoding         : utf-8

 Date: 05/05/2016 16:00:50 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `migrations`
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Records of `migrations`
-- ----------------------------
BEGIN;
INSERT INTO `migrations` VALUES ('2014_10_12_000000_create_users_table', '1'), ('2014_10_12_100000_create_password_resets_table', '1'), ('2016_04_28_065700_entrust_setup_tables', '2');
COMMIT;

-- ----------------------------
--  Table structure for `password_resets`
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Table structure for `permission_role`
-- ----------------------------
DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Records of `permission_role`
-- ----------------------------
BEGIN;
INSERT INTO `permission_role` VALUES ('3', '2'), ('5', '2'), ('6', '2'), ('2', '3'), ('3', '3'), ('4', '3'), ('16', '3');
COMMIT;

-- ----------------------------
--  Table structure for `permissions`
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT '0' COMMENT '父级id',
  `is_menu` tinyint(4) DEFAULT '0' COMMENT '为1时是菜单',
  `icon` varchar(50) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '图标',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '唯一名称即：路由',
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '显示名称',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '描述',
  `display_order` int(11) DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Records of `permissions`
-- ----------------------------
BEGIN;
INSERT INTO `permissions` VALUES ('1', '0', '1', '#xe62d;', '/perm', '管理员管理', '这是管理员管理菜单 给与权限请慎重 建议紧最高管理员拥有权限', '500', '2016-04-29 11:30:27', '2016-05-03 03:12:27'), ('2', '1', '1', '', '/perm/role', '角色管理', '角色的添加与修改 具有查看权限', '501', '2016-05-03 03:51:31', '2016-05-03 07:59:21'), ('3', '1', '1', '', '/perm/menu', '菜单与权限管理', '权限的管理是基于菜单管理的  所以需要先建立菜单', '502', '2016-05-03 07:12:51', '2016-05-03 07:12:51'), ('4', '1', '1', '', '/perm/user', '管理员列表', '这是管理系统管理员的列表', '503', '2016-05-03 07:14:03', '2016-05-03 07:14:03'), ('5', '3', '0', null, '/perm/menu/create', '菜单添加', '菜单的添加', '11', '2016-05-03 07:35:42', '2016-05-03 09:16:00'), ('6', '3', '0', '', '/perm/menu/{menu}/update', '菜单修改', '菜单的修改', '12', '2016-05-03 08:51:18', '2016-05-03 09:16:12'), ('7', '3', '0', '', '/perm/menu/{menu}/order', '排序', '菜单和权限的排序', '19', '2016-05-03 09:19:16', '2016-05-05 03:49:56'), ('8', '3', '0', '', '/perm/menu/{menu}/delete', '菜单删除', '菜单的删除', '14', '2016-05-03 09:19:44', '2016-05-03 09:19:44'), ('9', '3', '0', '', '/perm/menu/{menu}/permission', '权限查看', '该菜单下对应权限的查看', '15', '2016-05-03 09:20:28', '2016-05-03 09:20:28'), ('10', '3', '0', '', '/perm/permission/create/{menu}', '权限添加', '指定菜单下权限的添加', '16', '2016-05-03 09:21:07', '2016-05-03 09:21:07'), ('11', '3', '0', '', '/perm/permission/{permission}/update', '权限修改', '权限的修改', '17', '2016-05-03 09:21:54', '2016-05-03 09:21:54'), ('12', '3', '0', '', '/perm/permission/{permission}/delete', '权限的删除', '权限的删除', '18', '2016-05-03 09:22:22', '2016-05-03 09:22:22'), ('15', '0', '1', '#xe62e;', '/sys', '系统管理', '一些系统常用操作的管理', '700', '2016-05-05 03:45:45', '2016-05-05 03:45:45'), ('16', '15', '1', '', '/sys/log', '系统日志', '一些系统日常日志', '701', '2016-05-05 03:46:48', '2016-05-05 03:46:48'), ('17', '2', '0', '', '/perm/role/create', '添加', '角色添加', '10', '2016-05-05 03:47:55', '2016-05-05 03:47:55'), ('18', '2', '0', '', '/perm/role/{role}/update', '修改', '角色修改', '11', '2016-05-05 03:48:35', '2016-05-05 03:48:35'), ('19', '2', '0', '', '/perm/role/{role}/delete', '删除', '角色删除', '13', '2016-05-05 03:48:56', '2016-05-05 03:48:56'), ('20', '4', '0', '', '/perm/user/create', '添加', '管理员添加', '10', '2016-05-05 03:50:40', '2016-05-05 03:50:40'), ('21', '4', '0', '', '/perm/user/{user}/update', '修改', '管理员修改', '11', '2016-05-05 03:51:10', '2016-05-05 03:51:10'), ('22', '4', '0', '', '/perm/user/{user}/delete', '删除', '管理员删除', '13', '2016-05-05 03:51:48', '2016-05-05 03:51:48'), ('23', '4', '0', '', '/perm/user/{user}/password', '修改密码', '个人修改密码', '14', '2016-05-05 03:52:34', '2016-05-05 03:52:34'), ('24', '4', '0', '', '/perm/user/{user}/reset', '重置密码', '管理员重置密码', '15', '2016-05-05 03:53:01', '2016-05-05 03:53:01');
COMMIT;

-- ----------------------------
--  Table structure for `roles`
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '角色名',
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '角色昵称',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '角色描述',
  `level` tinyint(4) DEFAULT '0' COMMENT '角色等级',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Records of `roles`
-- ----------------------------
BEGIN;
INSERT INTO `roles` VALUES ('1', 'Admin', '最高管理员', '这是网站的最高权限管理员 网站拥有者', '100', '2016-05-04 09:30:05', '2016-05-04 09:30:05'), ('2', 'demo', '测试用户', '此用户用来测试判断权限等级', '0', '2016-05-04 10:16:33', '2016-05-04 10:16:33'), ('3', 'editor', '编辑', '网站负责编辑工作', '0', '2016-05-04 10:17:15', '2016-05-04 10:17:15');
COMMIT;

-- ----------------------------
--  Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `role_id` int(11) DEFAULT '0' COMMENT '角色id',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Records of `users`
-- ----------------------------
BEGIN;
INSERT INTO `users` VALUES ('1', 'Admin', 'admin@admin.com', '$2y$10$FBvKsEbU6gAxXlQ9Vyxl8eh7hja/CJQZKjI4Ke7YOPpEb6bjMJv.y', 'VLm09jiZmkvgrXXDkmPQP0iGNbYhXQTx8eZJRsVgW66CCCSgDbqFzcSwGhop', '1', '2016-04-28 18:26:04', '2016-05-05 06:09:57'), ('2', 'haha', '123456@qq.com', '$2y$10$al873oFlbGEaeJ4diIqyO.u4sD/KGlot1C9WsWMTmsDBThTBzQN7y', 'K4yPaeoO5WDzvUzqAs0TbxFysGtfrQMkaOpsKlABo0W2h22xK8GIJNNukRZo', '2', '2016-05-04 11:20:25', '2016-05-05 06:59:47'), ('3', 'demo', 'demo@demo.com', '$2y$10$Lv9x.5MiflDQ4k0PEYbe4uIueY78Zfurl1nzDib81H0fdPbq8ZXmm', '5nRosYBhkL6bCRbWlA1N7IPKSNDuFhjRaMEsFOgjUd5zqFiIjD468t2KY2E1', '3', '2016-05-05 02:01:58', '2016-05-05 06:09:32');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
