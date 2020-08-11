-- MySQL dump 10.13  Distrib 5.7.31, for Linux (x86_64)
--
-- Host: localhost    Database: nanocenter
-- ------------------------------------------------------
-- Server version	5.7.31-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `nanocenter`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `nanocenter` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `nanocenter`;

--
-- Table structure for table `account_categories`
--

DROP TABLE IF EXISTS `account_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `account_types`
--

DROP TABLE IF EXISTS `account_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account_types` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `account_users`
--

DROP TABLE IF EXISTS `account_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=MyISAM AUTO_INCREMENT=11011 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `account_id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `account_category` int(4) unsigned DEFAULT '0',
  `FRS` varchar(25) NOT NULL DEFAULT '' COMMENT 'account_code',
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
  `account_type` int(4) DEFAULT '0' COMMENT '0=internal, 1=external',
  `pi_email` varchar(255) DEFAULT NULL,
  `added_by` int(4) DEFAULT NULL,
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1686 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `announcementid` varchar(16) NOT NULL DEFAULT '',
  `announcement` text,
  `number` smallint(3) NOT NULL DEFAULT '0',
  `start_datetime` int(11) DEFAULT NULL,
  `end_datetime` int(11) DEFAULT NULL,
  `lab_id` int(4) DEFAULT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`announcementid`),
  KEY `announcements_startdatetime` (`start_datetime`),
  KEY `announcements_enddatetime` (`end_datetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `billing_imported`
--

DROP TABLE IF EXISTS `billing_imported`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_imported` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `billing_month` varchar(15) DEFAULT NULL,
  `billed` varchar(10) DEFAULT NULL,
  `Lab` varchar(50) DEFAULT NULL,
  `account_id` int(4) DEFAULT '0',
  `FRS` varchar(40) DEFAULT NULL,
  `Equipment` varchar(255) DEFAULT NULL,
  `PI/Advisor First Name` varchar(75) DEFAULT NULL,
  `PI/Advisor Last Name` varchar(75) DEFAULT NULL,
  `pi_id` int(4) DEFAULT '0',
  `User First Name` varchar(75) DEFAULT NULL,
  `User Last Name` varchar(75) DEFAULT NULL,
  `Pivot Table User` varchar(75) DEFAULT NULL,
  `User ID` int(4) DEFAULT '0',
  `Rate` float DEFAULT '0',
  `Amt Used` float DEFAULT '0',
  `Amount Billed` float DEFAULT '0',
  `NC Member` int(1) DEFAULT '0',
  `Transaction ID` varchar(20) DEFAULT NULL,
  `notes` text,
  `account_category` int(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  KEY `FRS` (`FRS`)
) ENGINE=MyISAM AUTO_INCREMENT=198458 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `current_fablab_resource_users`
--

DROP TABLE IF EXISTS `current_fablab_resource_users`;
/*!50001 DROP VIEW IF EXISTS `current_fablab_resource_users`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `current_fablab_resource_users` AS SELECT 
 1 AS `machid`,
 1 AS `user_id`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `current_nc_users_updated`
--

DROP TABLE IF EXISTS `current_nc_users_updated`;
/*!50001 DROP VIEW IF EXISTS `current_nc_users_updated`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `current_nc_users_updated` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `last_login`,
 1 AS `receive_announcements`,
 1 AS `deleted`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `fablab_equipment_fees_contact`
--

DROP TABLE IF EXISTS `fablab_equipment_fees_contact`;
/*!50001 DROP VIEW IF EXISTS `fablab_equipment_fees_contact`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `fablab_equipment_fees_contact` AS SELECT 
 1 AS `machid`,
 1 AS `short_name`,
 1 AS `name`,
 1 AS `category`,
 1 AS `umd_rate`,
 1 AS `nc_member_rate`,
 1 AS `maryland_system_rate`,
 1 AS `university_rate`,
 1 AS `government_rate`,
 1 AS `industry_rate`,
 1 AS `staff_contact`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `lab_permission`
--

DROP TABLE IF EXISTS `lab_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lab_permission` (
  `lab_id` int(4) NOT NULL DEFAULT '0',
  `user_id` int(4) NOT NULL DEFAULT '0',
  `is_admin` smallint(1) NOT NULL DEFAULT '0',
  `safety_trained` binary(1) NOT NULL DEFAULT '0',
  `trained_by` varchar(50) DEFAULT NULL,
  `trained_date` date DEFAULT NULL,
  `requested_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `userLabPermIndex` (`lab_id`,`user_id`),
  KEY `sp_scheduleid` (`lab_id`),
  KEY `sp_memberid` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labs`
--

DROP TABLE IF EXISTS `labs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labs` (
  `lab_id` varchar(16) NOT NULL,
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
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lab_id`),
  KEY `name` (`labTitle`(250)),
  KEY `manager` (`manager`(250))
) ENGINE=MyISAM AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `manufacturer`
--

DROP TABLE IF EXISTS `manufacturer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manufacturer` (
  `manufacturer_id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `contact_name` varchar(100) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(25) DEFAULT NULL,
  `contact_address` varchar(100) DEFAULT NULL,
  `contact_address2` varchar(100) DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`manufacturer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission` (
  `user_id` int(4) NOT NULL DEFAULT '0',
  `machid` char(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`,`machid`),
  KEY `per_memberid` (`user_id`),
  KEY `per_machid` (`machid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `previous_month_reservations_2_rates`
--

DROP TABLE IF EXISTS `previous_month_reservations_2_rates`;
/*!50001 DROP VIEW IF EXISTS `previous_month_reservations_2_rates`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `previous_month_reservations_2_rates` AS SELECT 
 1 AS `date`,
 1 AS `start`,
 1 AS `end`,
 1 AS `hours`,
 1 AS `name`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `user_email`,
 1 AS `user_id`,
 1 AS `resid`,
 1 AS `summary`,
 1 AS `account_id`,
 1 AS `technical_note`,
 1 AS `billing_note`,
 1 AS `resource_id`,
 1 AS `lab`,
 1 AS `lab_id`,
 1 AS `FRS`,
 1 AS `fed_id`,
 1 AS `pi`,
 1 AS `PI First Name`,
 1 AS `PI Last Name`,
 1 AS `pi_first_name`,
 1 AS `pi_last_name`,
 1 AS `Pivot Table Label`,
 1 AS `pi_nc_member`,
 1 AS `account_type`,
 1 AS `rate_type`,
 1 AS `rate`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `recipe_equipment_link`
--

DROP TABLE IF EXISTS `recipe_equipment_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipe_equipment_link` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `recipe_id` int(4) unsigned DEFAULT NULL,
  `equipment_id` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `recipe_FK_recipe_equipment_link` (`recipe_id`),
  KEY `equipment_FK_recipe_equipment_link` (`equipment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipes` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `recipe_text` text,
  `files` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reservation_users`
--

DROP TABLE IF EXISTS `reservation_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation_users` (
  `resid` char(16) NOT NULL DEFAULT '',
  `user_id` int(4) NOT NULL DEFAULT '0',
  `owner` smallint(1) DEFAULT NULL,
  `invited` smallint(1) DEFAULT NULL,
  `perm_modify` smallint(1) DEFAULT NULL,
  `perm_delete` smallint(1) DEFAULT NULL,
  `accept_code` char(16) DEFAULT NULL,
  PRIMARY KEY (`resid`,`user_id`),
  KEY `resusers_resid` (`resid`),
  KEY `resusers_memberid` (`user_id`),
  KEY `resusers_owner` (`owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservations` (
  `resid` varchar(16) NOT NULL DEFAULT '',
  `machid` varchar(16) NOT NULL DEFAULT '',
  `lab_id` varchar(16) NOT NULL DEFAULT '',
  `start_date` int(11) NOT NULL DEFAULT '0',
  `end_date` int(11) NOT NULL DEFAULT '0',
  `startTime` int(11) NOT NULL DEFAULT '0',
  `endTime` int(11) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `modified` int(11) DEFAULT NULL,
  `parentid` varchar(16) DEFAULT NULL,
  `is_blackout` smallint(1) NOT NULL DEFAULT '0',
  `is_pending` smallint(1) NOT NULL DEFAULT '0',
  `summary` text,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  `account_id` varchar(45) DEFAULT NULL,
  `technical_note` longtext,
  `billing_note` longtext,
  `created_by` int(4) DEFAULT NULL,
  `modified_by` int(4) DEFAULT NULL,
  `deleted` binary(1) DEFAULT '0',
  `deleted_by` int(4) DEFAULT NULL,
  `deleted_tstamp` timestamp NULL DEFAULT NULL,
  `deleted_reason` text,
  `archived` tinyint(1) NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_categories`
--

DROP TABLE IF EXISTS `resource_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resource_categories` (
  `title` varchar(50) NOT NULL,
  `discussion_link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_log`
--

DROP TABLE IF EXISTS `resource_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resource_log` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `machid` varchar(16) DEFAULT NULL,
  `user_id` int(4) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `log_text` text,
  `category` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_resource_log_user_id` (`user_id`),
  KEY `fk_machid` (`machid`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource_rates`
--

DROP TABLE IF EXISTS `resource_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resource_rates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `resource_id` varchar(16) NOT NULL DEFAULT '0',
  `account_type_id` int(4) NOT NULL DEFAULT '0',
  `rate` float NOT NULL DEFAULT '0',
  `rate_unit` varchar(16) DEFAULT 'hour',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3369 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resources` (
  `machid` varchar(16) NOT NULL DEFAULT '',
  `lab_id` varchar(16) NOT NULL DEFAULT '',
  `short_name` varchar(16) DEFAULT NULL,
  `name` varchar(75) NOT NULL DEFAULT '',
  `location` varchar(250) DEFAULT NULL,
  `rphone` varchar(16) DEFAULT NULL,
  `notes` text,
  `specifications` text,
  `webcam` text,
  `status` char(1) NOT NULL DEFAULT 'a',
  `minRes` int(11) NOT NULL DEFAULT '0',
  `maxRes` int(11) NOT NULL DEFAULT '0',
  `autoAssign` smallint(1) DEFAULT NULL,
  `approval` smallint(1) DEFAULT NULL,
  `allow_multi` smallint(1) DEFAULT NULL,
  `umd_rate` float DEFAULT '70',
  `nc_member_rate` float DEFAULT '22',
  `maryland_system_rate` float DEFAULT '77',
  `university_rate` float DEFAULT '77',
  `government_rate` float DEFAULT '77',
  `industry_rate` float DEFAULT '225',
  `owner` varchar(45) DEFAULT NULL,
  `edit_horizon` int(2) DEFAULT '-12',
  `manufacturer` varchar(50) DEFAULT NULL,
  `manufacturer_link` varchar(255) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `quick_info` text,
  `general_info` text,
  `staff_contact` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`machid`),
  KEY `rs_machid` (`machid`),
  KEY `rs_scheduleid` (`lab_id`),
  KEY `rs_name` (`name`),
  KEY `rs_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `umd_colleges`
--

DROP TABLE IF EXISTS `umd_colleges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `umd_colleges` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) DEFAULT NULL,
  `code` char(20) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `umd_departments`
--

DROP TABLE IF EXISTS `umd_departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `umd_departments` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) DEFAULT NULL,
  `code` char(20) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `college_id` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(4) NOT NULL AUTO_INCREMENT,
  `umd_uid` varchar(9) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `salutation` varchar(10) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `rank` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `cell_phone` varchar(20) DEFAULT NULL,
  `home_phone` varchar(20) DEFAULT NULL,
  `work_title` varchar(255) DEFAULT NULL,
  `work_phone` varchar(50) DEFAULT NULL,
  `work_address` varchar(255) DEFAULT NULL,
  `work_address2` varchar(100) DEFAULT NULL,
  `work_city` varchar(100) DEFAULT NULL,
  `work_state` varchar(50) DEFAULT NULL,
  `work_country` varchar(50) DEFAULT NULL,
  `work_zip` varchar(50) DEFAULT NULL,
  `lab_id` int(4) DEFAULT NULL,
  `timestamp_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `university` varchar(100) DEFAULT NULL,
  `biography` text,
  `affiliations` text,
  `visibility` int(1) DEFAULT '0' COMMENT 'For listing on staff/faculty lists. 0 is off 1 is on.',
  `webpage` varchar(255) DEFAULT NULL,
  `group_site` varchar(255) DEFAULT NULL,
  `department_page` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `advisor` int(4) DEFAULT NULL,
  `comments` text,
  `p/s` char(1) DEFAULT NULL COMMENT 'primary or secondary (p or s)',
  `type_id` int(2) DEFAULT '0' COMMENT 'user type from table:user_type',
  `exec_type_id` int(11) DEFAULT '0' COMMENT 'what type of exec user from table:executive_user_type',
  `research_interests` text,
  `password` varchar(255) DEFAULT NULL,
  `rights` int(1) DEFAULT '0',
  `department_id` int(4) DEFAULT NULL,
  `relationship` text,
  `supervisor` varchar(255) DEFAULT NULL,
  `register_status` int(1) DEFAULT '0',
  `intranet_access` int(1) DEFAULT '0',
  `receive_announcements` int(1) DEFAULT '0',
  `publish_email_on_site` char(1) DEFAULT '0',
  `is_collaborator` int(1) DEFAULT '0',
  `collaboration_info` text,
  `login_count` int(5) DEFAULT '0',
  `memberid` varchar(16) DEFAULT '',
  `institution` varchar(255) DEFAULT NULL,
  `e_add` char(1) DEFAULT 'y',
  `e_mod` char(1) DEFAULT 'y',
  `e_del` char(1) DEFAULT 'y',
  `e_app` char(1) DEFAULT 'y',
  `e_html` char(1) DEFAULT 'y',
  `lab_pref` int(3) DEFAULT NULL,
  `logon_name` varchar(30) DEFAULT NULL,
  `is_admin` smallint(1) DEFAULT '0',
  `cnst_member` tinyint(4) DEFAULT '0',
  `nc_member` tinyint(1) DEFAULT '0',
  `remote_ip` varchar(255) DEFAULT NULL,
  `deleted` int(1) DEFAULT '0',
  `register_site` varchar(100) DEFAULT NULL,
  `researcher_id` varchar(100) DEFAULT NULL,
  `orcid` varchar(100) DEFAULT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `type_id` (`type_id`),
  KEY `department_id` (`department_id`),
  KEY `lab_id` (`lab_id`),
  KEY `lname` (`last_name`),
  KEY `fname` (`first_name`)
) ENGINE=MyISAM AUTO_INCREMENT=17084 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `group_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_group_link`
--

DROP TABLE IF EXISTS `user_group_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group_link` (
  `group_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_resource_filters`
--

DROP TABLE IF EXISTS `user_resource_filters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_resource_filters` (
  `user_id` varchar(16) NOT NULL DEFAULT '',
  `machid` varchar(16) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`,`machid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_type`
--

DROP TABLE IF EXISTS `user_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_type` (
  `user_type_id` int(4) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `createAccount` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`user_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Current Database: `nanocenter`
--

USE `nanocenter`;

--
-- Final view structure for view `current_fablab_resource_users`
--

/*!50001 DROP VIEW IF EXISTS `current_fablab_resource_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `current_fablab_resource_users` AS select `r`.`machid` AS `machid`,`p`.`user_id` AS `user_id` from (`resources` `r` join `permission` `p` on((`r`.`machid` = `p`.`machid`))) where (`r`.`lab_id` = 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `current_nc_users_updated`
--

/*!50001 DROP VIEW IF EXISTS `current_nc_users_updated`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `current_nc_users_updated` AS select distinct `ru`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`u`.`last_login` AS `last_login`,`u`.`receive_announcements` AS `receive_announcements`,`u`.`deleted` AS `deleted` from (((`reservations` `r` join `reservation_users` `ru` on((`r`.`resid` = `ru`.`resid`))) join `user` `u` on((`ru`.`user_id` = `u`.`user_id`))) join `resources` `re` on((`re`.`machid` = `r`.`machid`))) where ((from_unixtime(`r`.`start_date`) between (curdate() - interval 60 day) and curdate()) and (`u`.`deleted` = 0)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `fablab_equipment_fees_contact`
--

/*!50001 DROP VIEW IF EXISTS `fablab_equipment_fees_contact`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `fablab_equipment_fees_contact` AS select `resources`.`machid` AS `machid`,`resources`.`short_name` AS `short_name`,`resources`.`name` AS `name`,`resources`.`category` AS `category`,`resources`.`umd_rate` AS `umd_rate`,`resources`.`nc_member_rate` AS `nc_member_rate`,`resources`.`maryland_system_rate` AS `maryland_system_rate`,`resources`.`university_rate` AS `university_rate`,`resources`.`government_rate` AS `government_rate`,`resources`.`industry_rate` AS `industry_rate`,`resources`.`staff_contact` AS `staff_contact` from `resources` where (`resources`.`lab_id` = 1) order by `resources`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `previous_month_reservations_2_rates`
--

/*!50001 DROP VIEW IF EXISTS `previous_month_reservations_2_rates`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `previous_month_reservations_2_rates` AS select date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8mb4'JIS')) AS `date`,date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(TIME, _utf8mb4'JIS')) AS `start`,date_format(from_unixtime((`r`.`end_date` + (60 * `r`.`endTime`))),get_format(TIME, _utf8mb4'JIS')) AS `end`,((time_to_sec(timediff(date_format(from_unixtime((`r`.`endTime` * 60)),get_format(TIME, _utf8mb4'JIS')),date_format(from_unixtime((`r`.`startTime` * 60)),get_format(TIME, _utf8mb4'JIS')))) / 60) / 60) AS `hours`,`resources`.`name` AS `name`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `user_email`,`ru`.`user_id` AS `user_id`,`r`.`resid` AS `resid`,`r`.`summary` AS `summary`,`r`.`account_id` AS `account_id`,`r`.`technical_note` AS `technical_note`,`r`.`billing_note` AS `billing_note`,`r`.`machid` AS `resource_id`,`labs`.`nickname` AS `lab`,`resources`.`lab_id` AS `lab_id`,`a`.`FRS` AS `FRS`,`a`.`fed_id` AS `fed_id`,`a`.`pi` AS `pi`,`piu`.`first_name` AS `PI First Name`,`piu`.`last_name` AS `PI Last Name`,`a`.`pi_first_name` AS `pi_first_name`,`a`.`pi_last_name` AS `pi_last_name`,if(isnull(`a`.`pi`),`a`.`pi_last_name`,`piu`.`last_name`) AS `Pivot Table Label`,if((`piu`.`nc_member` <> _utf8mb4'0'),1,0) AS `pi_nc_member`,`a`.`account_type` AS `account_type`,`at`.`label` AS `rate_type`,`rr`.`rate` AS `rate` from (((((((((`reservations` `r` join `resources` on((`r`.`machid` = `resources`.`machid`))) join `reservation_users` `ru` on((`r`.`resid` = `ru`.`resid`))) join `user` `u` on((`ru`.`user_id` = `u`.`user_id`))) join `labs` on((`resources`.`lab_id` = `labs`.`lab_id`))) left join `user` `ua` on((`u`.`advisor` = `ua`.`user_id`))) left join `accounts` `a` on((`r`.`account_id` = `a`.`account_id`))) left join `user` `piu` on((`a`.`pi` = `piu`.`user_id`))) left join `account_types` `at` on((`at`.`id` = `a`.`account_type`))) left join `resource_rates` `rr` on(((`rr`.`resource_id` = `r`.`machid`) and (`rr`.`account_type_id` = `a`.`account_type`)))) where ((date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8mb4'JIS')) between date_format((now() - interval 1 month),_utf8mb4'%Y-%m-16') and date_format(now(),_utf8mb4'%Y-%m-15')) and (`r`.`deleted` = 0)) order by date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8mb4'JIS')),`a`.`pi_last_name`,`a`.`pi_first_name`,`u`.`last_name`,`u`.`first_name`,`resources`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-08-11 20:01:22
