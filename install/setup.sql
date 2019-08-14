/*
SQLyog Enterprise v12.02 (64 bit)
MySQL - 5.1.63-0ubuntu0.10.04.1 : Database - nanoprod
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`nanoprod` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `nanoprod`;

/*Table structure for table `account_users` */

DROP TABLE IF EXISTS `account_users`;

CREATE TABLE `account_users` (
  `account_users_id` int(4) NOT NULL AUTO_INCREMENT,
  `account_id` int(4) NOT NULL DEFAULT '0',
  `user_id` int(4) NOT NULL DEFAULT '0',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=off, 1=on',
  `is_admin` binary(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`account_users_id`),
  KEY `user_id` (`user_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4925 DEFAULT CHARSET=utf8;

/*Table structure for table `accounts` */

DROP TABLE IF EXISTS `accounts`;

CREATE TABLE `accounts` (
  `account_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `FRS` varchar(25) NOT NULL COMMENT 'account_code',
  `sub_FRS` varchar(25) DEFAULT NULL,
  `pi` int(4) DEFAULT NULL,
  `pi_first_name` varchar(45) DEFAULT NULL,
  `pi_last_name` varchar(45) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
  `archived` tinyint(1) DEFAULT '0',
  `admin_unit` varchar(75) DEFAULT NULL,
  `name` text,
  `start_date` text,
  `end_date` text,
  `comments` text,
  `source` varchar(75) DEFAULT NULL,
  `agency` varchar(75) DEFAULT NULL,
  `confirmed` binary(1) DEFAULT '0',
  `last_update` date DEFAULT NULL,
  `fed_id` varchar(30) DEFAULT NULL,
  `admin_contact_name` varchar(255) DEFAULT NULL,
  `admin_contact_email` varchar(255) DEFAULT NULL,
  `admin_contact_phone` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `billing_address1` varchar(255) DEFAULT NULL,
  `billing_address2` varchar(255) DEFAULT NULL,
  `billing_city` varchar(255) DEFAULT NULL,
  `billing_state` varchar(30) DEFAULT NULL,
  `billing_zip` varchar(15) DEFAULT NULL,
  `business_contact_name` varchar(255) DEFAULT NULL,
  `business_contact_phone` varchar(25) DEFAULT NULL,
  `business_contact_email` varchar(255) DEFAULT NULL,
  `technical_contact_name` varchar(255) DEFAULT NULL,
  `technical_contact_phone` varchar(25) DEFAULT NULL,
  `technical_contact_email` varchar(255) DEFAULT NULL,
  `reviewed` tinyint(4) NOT NULL DEFAULT '0',
  `reviewed_by` int(2) DEFAULT NULL,
  `account_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=internal, 1=external',
  `pi_email` varchar(255) DEFAULT NULL,
  `added_by` int(4) DEFAULT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1109 DEFAULT CHARSET=utf8;

/*Table structure for table `announcements` */

DROP TABLE IF EXISTS `announcements`;

CREATE TABLE `announcements` (
  `announcementid` varchar(16) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `announcement` text CHARACTER SET utf8,
  `number` smallint(3) NOT NULL DEFAULT '0',
  `start_datetime` int(11) DEFAULT NULL,
  `end_datetime` int(11) DEFAULT NULL,
  `lab_id` int(4) DEFAULT NULL,
  PRIMARY KEY (`announcementid`),
  KEY `announcements_startdatetime` (`start_datetime`),
  KEY `announcements_enddatetime` (`end_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `billing_imported` */

DROP TABLE IF EXISTS `billing_imported`;

CREATE TABLE `billing_imported` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `billing_month` varchar(15) CHARACTER SET utf8 DEFAULT NULL,
  `billed` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `Lab` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `account_id` int(4) DEFAULT '0',
  `FRS` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `Equipment` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `PI/Advisor First Name` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  `PI/Advisor Last Name` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  `pi_id` int(4) DEFAULT '0',
  `User First Name` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  `User Last Name` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  `Pivot Table User` varchar(75) CHARACTER SET utf8 DEFAULT NULL,
  `User ID` int(4) DEFAULT '0',
  `Rate` float DEFAULT '0',
  `Amt Used` float DEFAULT '0',
  `Amount Billed` float DEFAULT '0',
  `NC Member` int(1) DEFAULT '0',
  `Transaction ID` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `notes` text CHARACTER SET utf8,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `FRS` (`FRS`)
) ENGINE=InnoDB AUTO_INCREMENT=137526 DEFAULT CHARSET=utf8;

/*Table structure for table `lab_permission` */

DROP TABLE IF EXISTS `lab_permission`;

CREATE TABLE `lab_permission` (
  `lab_id` int(4) NOT NULL DEFAULT '0',
  `user_id` int(4) NOT NULL DEFAULT '0',
  `is_admin` smallint(1) NOT NULL DEFAULT '0',
  `safety_trained` binary(1) NOT NULL DEFAULT '0',
  `trained_by` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `trained_date` date DEFAULT NULL,
  `requested_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `userLabPermIndex` (`lab_id`,`user_id`),
  KEY `sp_scheduleid` (`lab_id`),
  KEY `sp_memberid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Table structure for table `labs` */

DROP TABLE IF EXISTS `labs`;

CREATE TABLE `labs` (
  `lab_id` int(4) NOT NULL AUTO_INCREMENT,
  `labTitle` varchar(255) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `description` text,
  `director` varchar(255) DEFAULT NULL,
  `manager` varchar(255) DEFAULT NULL,
  `building` varchar(255) DEFAULT NULL,
  `room_number` int(5) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `priority` int(1) NOT NULL DEFAULT '1',
  `summary` text,
  `type` varchar(30) DEFAULT NULL,
  `visibility` int(1) NOT NULL DEFAULT '1',
  `dayStart` int(11) NOT NULL DEFAULT '0',
  `dayEnd` int(11) NOT NULL DEFAULT '0',
  `timeSpan` int(11) NOT NULL DEFAULT '0',
  `timeFormat` int(11) NOT NULL DEFAULT '0',
  `weekDayStart` int(11) NOT NULL DEFAULT '0',
  `viewDays` int(11) NOT NULL DEFAULT '0',
  `usePermissions` smallint(1) NOT NULL DEFAULT '1',
  `isHidden` smallint(1) NOT NULL DEFAULT '0',
  `showSummary` smallint(1) DEFAULT NULL,
  `adminEmail` varchar(75) DEFAULT NULL,
  `isDefault` smallint(1) DEFAULT NULL,
  `dayOffset` int(11) DEFAULT NULL,
  `scheduler` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lab_id`),
  KEY `name` (`labTitle`),
  KEY `manager` (`manager`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Table structure for table `permission` */

DROP TABLE IF EXISTS `permission`;

CREATE TABLE `permission` (
  `user_id` int(4) NOT NULL DEFAULT '0',
  `machid` char(16) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`,`machid`),
  KEY `per_memberid` (`user_id`),
  KEY `per_machid` (`machid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `reservation_users` */

DROP TABLE IF EXISTS `reservation_users`;

CREATE TABLE `reservation_users` (
  `resid` char(16) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `user_id` int(4) NOT NULL DEFAULT '0',
  `owner` smallint(1) DEFAULT NULL,
  `invited` smallint(1) DEFAULT NULL,
  `perm_modify` smallint(1) DEFAULT NULL,
  `perm_delete` smallint(1) DEFAULT NULL,
  `accept_code` char(16) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`resid`,`user_id`),
  KEY `resusers_resid` (`resid`),
  KEY `resusers_memberid` (`user_id`),
  KEY `resusers_owner` (`owner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `reservations` */

DROP TABLE IF EXISTS `reservations`;

CREATE TABLE `reservations` (
  `resid` varchar(16) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `machid` varchar(16) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `lab_id` varchar(16) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `start_date` int(11) NOT NULL DEFAULT '0',
  `end_date` int(11) NOT NULL DEFAULT '0',
  `startTime` int(11) NOT NULL DEFAULT '0',
  `endTime` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `modified` int(11) DEFAULT NULL,
  `parentid` varchar(16) CHARACTER SET utf8 DEFAULT NULL,
  `is_blackout` smallint(1) NOT NULL DEFAULT '0',
  `is_pending` smallint(1) NOT NULL DEFAULT '0',
  `summary` text CHARACTER SET utf8,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `account_id` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `technical_note` longtext CHARACTER SET utf8,
  `billing_note` longtext CHARACTER SET utf8,
  `created_by` int(4) DEFAULT NULL,
  `modified_by` int(4) DEFAULT NULL,
  `deleted` binary(1) DEFAULT '0',
  `deleted_by` int(4) DEFAULT NULL,
  `deleted_tstamp` timestamp NULL DEFAULT NULL,
  `deleted_reason` text CHARACTER SET utf8,
  PRIMARY KEY (`resid`),
  KEY `res_resid` (`resid`),
  KEY `res_machid` (`machid`),
  KEY `res_scheduleid` (`lab_id`),
  KEY `reservations_startdate` (`start_date`),
  KEY `reservations_enddate` (`end_date`),
  KEY `res_startTime` (`startTime`),
  KEY `res_endTime` (`endTime`),
  KEY `res_created` (`created`),
  KEY `res_modified` (`modified`),
  KEY `res_parentid` (`parentid`),
  KEY `res_isblackout` (`is_blackout`),
  KEY `reservations_pending` (`is_pending`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `resources` */

DROP TABLE IF EXISTS `resources`;

CREATE TABLE `resources` (
  `machid` varchar(16) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `lab_id` varchar(16) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `short_name` varchar(16) CHARACTER SET utf8 DEFAULT NULL,
  `name` varchar(75) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `location` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `rphone` varchar(16) CHARACTER SET utf8 DEFAULT NULL,
  `notes` text CHARACTER SET utf8,
  `status` char(1) CHARACTER SET utf8 NOT NULL DEFAULT 'a',
  `minRes` int(11) NOT NULL DEFAULT '0',
  `maxRes` int(11) NOT NULL DEFAULT '0',
  `autoAssign` smallint(1) DEFAULT NULL,
  `approval` smallint(1) DEFAULT NULL,
  `allow_multi` smallint(1) DEFAULT NULL,
  `umd_rate` int(4) DEFAULT '70',
  `nc_member_rate` int(4) DEFAULT '22',
  `maryland_system_rate` int(4) DEFAULT '77',
  `university_rate` int(4) DEFAULT '77',
  `government_rate` int(4) DEFAULT '77',
  `industry_rate` int(4) DEFAULT '225',
  `owner` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `edit_horizon` int(2) DEFAULT '-12',
  `manufacturer` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `manufacturer_link` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `model` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `quick_info` text CHARACTER SET utf8,
  `general_info` text CHARACTER SET utf8,
  `staff_contact` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `category` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `deleted` binary(0) DEFAULT NULL,
  PRIMARY KEY (`machid`),
  KEY `rs_machid` (`machid`),
  KEY `rs_scheduleid` (`lab_id`),
  KEY `rs_name` (`name`),
  KEY `rs_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `user_id` int(4) NOT NULL AUTO_INCREMENT,
  `umd_uid` varchar(9) CHARACTER SET utf8 DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `salutation` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `first_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `rank` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `cell_phone` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `home_phone` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `work_title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `work_phone` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `work_address` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `work_address2` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `work_city` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `work_state` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `work_country` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `work_zip` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `lab_id` int(4) DEFAULT NULL,
  `timestamp_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `university` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `biography` text CHARACTER SET utf8,
  `affiliations` text CHARACTER SET utf8,
  `visibility` int(1) DEFAULT '0' COMMENT 'For listing on staff/faculty lists. 0 is off 1 is on.',
  `webpage` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `group_site` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `department_page` varchar(255) DEFAULT NULL,
  `organization` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `department` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `advisor` int(4) DEFAULT NULL,
  `comments` text CHARACTER SET utf8,
  `p/s` char(1) CHARACTER SET utf8 DEFAULT NULL COMMENT 'primary or secondary (p or s)',
  `type_id` int(2) DEFAULT '0' COMMENT 'user type from table:user_type',
  `exec_type_id` int(11) DEFAULT '0' COMMENT 'what type of exec user from table:executive_user_type',
  `research_interests` text CHARACTER SET utf8,
  `password` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `rights` int(1) DEFAULT '0',
  `department_id` int(4) DEFAULT NULL,
  `relationship` text CHARACTER SET utf8,
  `supervisor` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `register_status` int(1) DEFAULT '0',
  `intranet_access` int(1) DEFAULT '0',
  `receive_announcements` int(1) DEFAULT '0',
  `publish_email_on_site` char(1) CHARACTER SET utf8 DEFAULT '0',
  `is_collaborator` int(1) DEFAULT '0',
  `collaboration_info` text CHARACTER SET utf8,
  `login_count` int(5) DEFAULT '0',
  `memberid` varchar(16) CHARACTER SET utf8 DEFAULT '',
  `institution` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `e_add` char(1) CHARACTER SET utf8 DEFAULT 'y',
  `e_mod` char(1) CHARACTER SET utf8 DEFAULT 'y',
  `e_del` char(1) CHARACTER SET utf8 DEFAULT 'y',
  `e_app` char(1) CHARACTER SET utf8 DEFAULT 'y',
  `e_html` char(1) CHARACTER SET utf8 DEFAULT 'y',
  `lab_pref` int(3) DEFAULT NULL,
  `logon_name` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  `is_admin` smallint(1) DEFAULT '0',
  `cnst_member` tinyint(4) DEFAULT '0',
  `nc_member` tinyint(1) DEFAULT '0',
  `remote_ip` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `deleted` binary(1) DEFAULT '0',
  `register_site` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `researcher_id` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `type_id` (`type_id`),
  KEY `department_id` (`department_id`),
  KEY `lab_id` (`lab_id`),
  KEY `lname` (`last_name`),
  KEY `fname` (`first_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4231 DEFAULT CHARSET=utf8;

/*Table structure for table `user_type` */

DROP TABLE IF EXISTS `user_type`;

CREATE TABLE `user_type` (
  `user_type_id` int(4) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `createAccount` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
