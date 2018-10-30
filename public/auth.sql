/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 5.5.53 : Database - auth
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`auth` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `auth`;

/*Table structure for table `access` */

DROP TABLE IF EXISTS `access`;

CREATE TABLE `access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级id',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '权限名称',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '1 菜单 2菜单+权限 3 权限',
  `url` varchar(1000) NOT NULL DEFAULT '' COMMENT '权限',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1：有效 0：无效',
  `updated_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后一次更新时间',
  `created_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '插入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='权限详情表';

/*Data for the table `access` */

insert  into `access`(`id`,`parent_id`,`title`,`type`,`url`,`status`,`updated_time`,`created_time`) values (1,0,'导航菜单',1,'',1,'2018-10-24 13:29:13','2018-10-23 17:47:02'),(2,1,'单页管理',1,'',1,'0000-00-00 00:00:00','2018-10-27 16:14:22'),(3,1,'系统管理',1,'',1,'0000-00-00 00:00:00','2018-10-27 16:17:09'),(4,2,'首页',2,'/index/index',1,'2018-10-29 15:26:21','2018-10-27 16:18:45'),(5,2,'测试页面1',2,'/test/page1',1,'2018-10-29 15:27:11','2018-10-29 12:41:04'),(6,2,'测试页面2',2,'/test/page2',1,'2018-10-29 15:27:16','2018-10-29 15:21:35'),(7,2,'测试页面3',2,'/test/page3',1,'2018-10-29 15:27:26','2018-10-29 15:22:13'),(8,2,'测试页面4',2,'/test/page4',1,'2018-10-29 15:27:31','2018-10-29 15:22:41'),(9,3,'用户管理',2,'/user/index',1,'0000-00-00 00:00:00','2018-10-29 15:23:23'),(10,3,'角色管理',2,'/role/index',1,'0000-00-00 00:00:00','2018-10-29 15:28:45'),(11,3,'权限管理',2,'/access/index',1,'0000-00-00 00:00:00','2018-10-29 15:29:30'),(12,9,'添加用户',3,'/user/create',1,'2018-10-29 15:39:06','2018-10-29 15:37:23'),(13,9,'编辑用户',3,'/user/edit',1,'0000-00-00 00:00:00','2018-10-29 15:38:04'),(14,10,'添加角色',3,'/role/create',1,'0000-00-00 00:00:00','2018-10-29 15:38:54'),(15,10,'编辑角色',3,'/role/edit',1,'2018-10-29 15:47:35','2018-10-29 15:40:25'),(16,10,'设置权限',3,'/role/access',1,'0000-00-00 00:00:00','2018-10-29 15:41:06'),(17,11,'添加权限',3,'/access/create',1,'0000-00-00 00:00:00','2018-10-29 15:46:31'),(18,11,'编辑权限',3,'/access/edit',1,'0000-00-00 00:00:00','2018-10-29 15:47:19');

/*Table structure for table `app_access_log` */

DROP TABLE IF EXISTS `app_access_log`;

CREATE TABLE `app_access_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL DEFAULT '0' COMMENT '品牌UID',
  `ua` varchar(255) NOT NULL DEFAULT '' COMMENT '访问ua',
  `ip` varchar(32) NOT NULL DEFAULT '' COMMENT '访问ip',
  `note` varchar(1000) NOT NULL DEFAULT '' COMMENT 'json格式备注字段',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='用户操作记录表';

/*Data for the table `app_access_log` */

insert  into `app_access_log`(`id`,`uid`,`ua`,`ip`,`note`,`created_time`) values (1,1,'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36','127.0.0.1','','2018-10-30 12:57:27'),(2,1,'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36','127.0.0.1','','2018-10-30 13:01:36');

/*Table structure for table `role` */

DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '角色名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1：有效 0：无效',
  `updated_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后一次更新时间',
  `created_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '插入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='角色表';

/*Data for the table `role` */

insert  into `role`(`id`,`name`,`status`,`updated_time`,`created_time`) values (1,'销售经理',1,'2018-10-30 11:57:01','2018-10-22 15:23:58'),(2,'销售',1,'2018-10-24 09:08:54','2018-10-22 15:32:41'),(3,'采购经理',1,'0000-00-00 00:00:00','2018-10-26 15:43:25'),(4,'采购',1,'0000-00-00 00:00:00','2018-10-26 15:43:39');

/*Table structure for table `role_access` */

DROP TABLE IF EXISTS `role_access`;

CREATE TABLE `role_access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
  `access_id` int(11) NOT NULL DEFAULT '0' COMMENT '权限id',
  `created_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '插入时间',
  PRIMARY KEY (`id`),
  KEY `idx_role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='角色权限表';

/*Data for the table `role_access` */

insert  into `role_access`(`id`,`role_id`,`access_id`,`created_time`) values (1,2,4,'2018-10-29 16:58:31'),(2,2,5,'2018-10-29 16:58:31'),(3,1,4,'2018-10-29 16:59:26'),(4,1,5,'2018-10-29 16:59:26'),(5,1,6,'2018-10-29 16:59:26'),(6,1,7,'2018-10-29 16:59:26'),(7,1,12,'2018-10-30 12:21:15');

/*Table structure for table `role_menu` */

DROP TABLE IF EXISTS `role_menu`;

CREATE TABLE `role_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色id',
  `access_id` int(11) NOT NULL DEFAULT '0' COMMENT '菜单权限id',
  `created_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '插入时间',
  PRIMARY KEY (`id`),
  KEY `idx_role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='角色菜单表';

/*Data for the table `role_menu` */

insert  into `role_menu`(`id`,`role_id`,`access_id`,`created_time`) values (1,2,1,'2018-10-29 16:58:31'),(2,2,2,'2018-10-29 16:58:31'),(3,2,4,'2018-10-29 16:58:31'),(4,2,5,'2018-10-29 16:58:31'),(5,1,1,'2018-10-29 16:59:26'),(6,1,2,'2018-10-29 16:59:26'),(7,1,4,'2018-10-29 16:59:26'),(8,1,5,'2018-10-29 16:59:26'),(9,1,6,'2018-10-29 16:59:26'),(10,1,7,'2018-10-29 16:59:26'),(11,1,3,'2018-10-30 12:21:15'),(12,1,9,'2018-10-30 12:21:15');

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '姓名',
  `password` varchar(50) NOT NULL COMMENT '密码',
  `email` varchar(30) NOT NULL DEFAULT '' COMMENT '邮箱',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是超级管理员 1表示是 0 表示不是',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1：有效 0：无效',
  `updated_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后一次更新时间',
  `created_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '插入时间',
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户表';

/*Data for the table `user` */

insert  into `user`(`id`,`name`,`password`,`email`,`is_admin`,`status`,`updated_time`,`created_time`) values (1,'admin','e10adc3949ba59abbe56e057f20f883e','2323178881@qq.com',1,1,'2018-10-23 15:16:40','2018-10-22 16:18:45'),(2,'zou','e10adc3949ba59abbe56e057f20f883e','zou@qq.com',0,1,'2018-10-30 11:45:45','2018-10-26 17:09:37'),(3,'wang','','wang@qq.com',0,1,'0000-00-00 00:00:00','2018-10-30 12:22:35');

/*Table structure for table `user_role` */

DROP TABLE IF EXISTS `user_role`;

CREATE TABLE `user_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `role_id` int(11) NOT NULL DEFAULT '0' COMMENT '角色ID',
  `created_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '插入时间',
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户角色表';

/*Data for the table `user_role` */

insert  into `user_role`(`id`,`uid`,`role_id`,`created_time`) values (2,2,1,'2018-10-29 17:29:38'),(3,3,2,'2018-10-30 12:22:35');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
