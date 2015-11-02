/*
SQLyog Community v12.12 (64 bit)
MySQL - 5.6.24 : Database - businessonmobile
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`businessonmobile` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `businessonmobile`;

/*Table structure for table `bom_manufacturers` */

DROP TABLE IF EXISTS `bom_manufacturers`;

CREATE TABLE `bom_manufacturers` (
  `manu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'FK:bom_users.user_id',
  `manu_brand_logo` blob,
  `manu_company_photo` blob,
  `manu_display_name` varchar(150) COLLATE utf8_bin NOT NULL,
  `manu_company_name` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `manu_email_id` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `manu_country` char(100) COLLATE utf8_bin DEFAULT NULL,
  `manu_state` char(100) COLLATE utf8_bin DEFAULT NULL,
  `manu_city` char(100) COLLATE utf8_bin DEFAULT NULL,
  `manu_country_code` char(10) COLLATE utf8_bin DEFAULT NULL,
  `manu_contact_number` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`manu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Table structure for table `bom_users` */

DROP TABLE IF EXISTS `bom_users`;

CREATE TABLE `bom_users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` enum('m','v','c') COLLATE utf8_bin NOT NULL COMMENT 'm:manufactures, v:vendor, c:customer',
  `login_email` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `social_type` enum('fb','ln','tw','gp') COLLATE utf8_bin DEFAULT NULL,
  `social_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `otp` int(6) DEFAULT NULL,
  `otp_expire_time` datetime DEFAULT NULL,
  `is_otp_verified` tinyint(4) DEFAULT '0',
  `device_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `device_type` enum('a','i') COLLATE utf8_bin NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  `is_notification` tinyint(4) NOT NULL DEFAULT '1',
  `access_token` varchar(200) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Table structure for table `bom_vendors` */

DROP TABLE IF EXISTS `bom_vendors`;

CREATE TABLE `bom_vendors` (
  `vndr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'FK:bom_users.user_id',
  `vndr_brand_logo` blob,
  `vndr_company_photo` blob,
  `vndr_display_name` varchar(150) COLLATE utf8_bin NOT NULL,
  `vndr_company_name` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `vndr_email_id` varchar(150) COLLATE utf8_bin DEFAULT NULL,
  `vndr_country` char(100) COLLATE utf8_bin DEFAULT NULL,
  `vndr_state` char(100) COLLATE utf8_bin DEFAULT NULL,
  `vndr_city` char(100) COLLATE utf8_bin DEFAULT NULL,
  `vndr_country_code` char(10) COLLATE utf8_bin DEFAULT NULL,
  `vndr_contact_number` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`vndr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
