-- MySQL dump 10.13  Distrib 5.7.32, for Linux (x86_64)
--
-- Host: localhost    Database: nanocenter
-- ------------------------------------------------------
-- Server version	5.7.32-0ubuntu0.18.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `2017_H1_aimlab_users`
--

DROP TABLE IF EXISTS `2017_H1_aimlab_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `2017_H1_aimlab_users` (
  `first_name` varchar(64) NOT NULL,
  `last_name` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `2017_H1_fablab_users`
--

DROP TABLE IF EXISTS `2017_H1_fablab_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `2017_H1_fablab_users` (
  `user_id` int(4) NOT NULL,
  `first_name` varchar(64) NOT NULL,
  `last_name` varchar(64) NOT NULL,
  `lab` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `EFRC Former Members`
--

DROP TABLE IF EXISTS `EFRC Former Members`;
/*!50001 DROP VIEW IF EXISTS `EFRC Former Members`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `EFRC Former Members` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `efrc_thrust`,
 1 AS `efrc_role`,
 1 AS `former_member`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `EFRC PostDocs Grads`
--

DROP TABLE IF EXISTS `EFRC PostDocs Grads`;
/*!50001 DROP VIEW IF EXISTS `EFRC PostDocs Grads`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `EFRC PostDocs Grads` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `efrc_role`,
 1 AS `efrc_thrust`,
 1 AS `efrc_approved`,
 1 AS `former_member`*/;
SET character_set_client = @saved_cs_client;

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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=11013 DEFAULT CHARSET=utf8;
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
  `deleted` int(4) DEFAULT '0',
  PRIMARY KEY (`account_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1686 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `accounts_copy`
--

DROP TABLE IF EXISTS `accounts_copy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts_copy` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1257 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `admin_accounts`
--

DROP TABLE IF EXISTS `admin_accounts`;
/*!50001 DROP VIEW IF EXISTS `admin_accounts`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `admin_accounts` AS SELECT 
 1 AS `account_id`,
 1 AS `FRS`,
 1 AS `sub_FRS`,
 1 AS `name`,
 1 AS `status`,
 1 AS `pi_last_name`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `aimlab_trained_users`
--

DROP TABLE IF EXISTS `aimlab_trained_users`;
/*!50001 DROP VIEW IF EXISTS `aimlab_trained_users`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `aimlab_trained_users` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `receive_announcements`,
 1 AS `deleted`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `all_roboticslab_users_updated`
--

DROP TABLE IF EXISTS `all_roboticslab_users_updated`;
/*!50001 DROP VIEW IF EXISTS `all_roboticslab_users_updated`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `all_roboticslab_users_updated` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `last_login`,
 1 AS `receive_announcements`,
 1 AS `deleted`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `amec_2015_reg`
--

DROP TABLE IF EXISTS `amec_2015_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `amec_2015_reg` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
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
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`announcementid`),
  KEY `announcements_startdatetime` (`start_datetime`),
  KEY `announcements_enddatetime` (`end_datetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `battery_bay_marker_tags`
--

DROP TABLE IF EXISTS `battery_bay_marker_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `battery_bay_marker_tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `battery_bay_marker_types`
--

DROP TABLE IF EXISTS `battery_bay_marker_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `battery_bay_marker_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `battery_bay_markers`
--

DROP TABLE IF EXISTS `battery_bay_markers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `battery_bay_markers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `x` float DEFAULT NULL,
  `y` float DEFAULT NULL,
  `weight` int(11) NOT NULL DEFAULT '1',
  `name` varchar(255) DEFAULT NULL,
  `type` int(11) unsigned DEFAULT NULL,
  `involvement` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`type`),
  KEY `fk_marker_type` (`type`),
  CONSTRAINT `fk_marker_type` FOREIGN KEY (`type`) REFERENCES `battery_bay_marker_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=198458 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `billing_imported_archived_2017-06`
--

DROP TABLE IF EXISTS `billing_imported_archived_2017-06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_imported_archived_2017-06` (
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
) ENGINE=MyISAM AUTO_INCREMENT=178658 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `billing_imported_copy`
--

DROP TABLE IF EXISTS `billing_imported_copy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `billing_imported_copy` (
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
) ENGINE=MyISAM AUTO_INCREMENT=197164 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `centers`
--

DROP TABLE IF EXISTS `centers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `centers` (
  `centers_id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `nickname` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `description` text CHARACTER SET latin1,
  `location` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `url` text CHARACTER SET latin1,
  `priority` int(1) NOT NULL DEFAULT '1',
  `visible` char(3) CHARACTER SET latin1 NOT NULL DEFAULT 'yes',
  `director` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`centers_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cnst_travel_award_applications`
--

DROP TABLE IF EXISTS `cnst_travel_award_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cnst_travel_award_applications` (
  `id` varchar(20) CHARACTER SET latin1 NOT NULL,
  `pi_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `address1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `address2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `city` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `state` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `zip` varchar(25) CHARACTER SET latin1 DEFAULT NULL,
  `phone` varchar(25) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `first_time_user` tinyint(3) unsigned DEFAULT NULL,
  `new_research` tinyint(3) unsigned DEFAULT NULL,
  `new_research_description` text CHARACTER SET latin1,
  `point_of_origin` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `means_of_travel` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `pi_days` int(10) unsigned DEFAULT NULL,
  `expected_start_date` varchar(30) DEFAULT NULL,
  `expected_end_date` varchar(30) DEFAULT NULL,
  `proposal` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `approval` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT NULL,
  `other_user_name_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_grad_student_1` tinyint(1) DEFAULT NULL,
  `other_user_institution_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_program_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_degree_date_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_advisor_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_email_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_name_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_grad_student_2` tinyint(1) DEFAULT NULL,
  `other_user_institution_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_program_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_degree_date_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_advisor_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_email_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_name_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_grad_student_3` tinyint(1) DEFAULT NULL,
  `other_user_institution_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_program_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_degree_date_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_advisor_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_email_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_name_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_grad_student_4` tinyint(1) DEFAULT NULL,
  `other_user_institution_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_program_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_degree_date_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_advisor_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_email_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_name_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_grad_student_5` tinyint(1) DEFAULT NULL,
  `other_user_institution_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_program_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_degree_date_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_advisor_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_email_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `refered_by_web` int(1) DEFAULT NULL,
  `refered_by_other` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `refered_by_mrs_ad` int(1) DEFAULT NULL,
  `refered_by_colleague` int(1) DEFAULT NULL,
  `refered_by_poster` int(1) DEFAULT NULL,
  `submitted` binary(1) DEFAULT '0',
  `submit_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reviewed_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cnst_travel_award_applications_backup`
--

DROP TABLE IF EXISTS `cnst_travel_award_applications_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cnst_travel_award_applications_backup` (
  `id` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `pi_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `address1` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `address2` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `city` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `state` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `zip` varchar(25) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `phone` varchar(25) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `email` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `first_time_user` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `new_research` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `new_research_description` text CHARACTER SET latin1,
  `point_of_origin` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `means_of_travel` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `pi_days` int(10) unsigned DEFAULT NULL,
  `expected_start_date` varchar(30) DEFAULT NULL,
  `expected_end_date` varchar(30) DEFAULT NULL,
  `proposal` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `approval` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `other_user_name_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_grad_student_1` tinyint(1) NOT NULL DEFAULT '0',
  `other_user_institution_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_program_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_degree_date_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_advisor_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_email_1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_name_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_grad_student_2` tinyint(1) NOT NULL DEFAULT '0',
  `other_user_institution_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_program_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_degree_date_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_advisor_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_email_2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_name_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_grad_student_3` tinyint(1) NOT NULL DEFAULT '0',
  `other_user_institution_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_program_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_degree_date_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_advisor_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_email_3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_name_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_grad_student_4` tinyint(1) NOT NULL DEFAULT '0',
  `other_user_institution_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_program_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_degree_date_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_advisor_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_email_4` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_name_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_grad_student_5` tinyint(1) NOT NULL DEFAULT '0',
  `other_user_institution_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_program_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_degree_date_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_advisor_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `other_user_email_5` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `refered_by_web` int(1) NOT NULL DEFAULT '0',
  `refered_by_other` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `refered_by_mrs_ad` int(1) NOT NULL DEFAULT '0',
  `refered_by_colleague` int(1) NOT NULL DEFAULT '0',
  `refered_by_poster` int(1) NOT NULL DEFAULT '0',
  `submitted` binary(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cnst_travel_award_users`
--

DROP TABLE IF EXISTS `cnst_travel_award_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cnst_travel_award_users` (
  `application_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET latin1 NOT NULL,
  `grad_student` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `institution` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `program` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `expected_degree` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `expected_degree_date` datetime DEFAULT '0000-00-00 00:00:00',
  `faculty_advisor` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`application_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cnst_users`
--

DROP TABLE IF EXISTS `cnst_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cnst_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `admin` binary(1) NOT NULL DEFAULT '0',
  `start_date` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conference_abstracts`
--

DROP TABLE IF EXISTS `conference_abstracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conference_abstracts` (
  `id` varchar(16) NOT NULL DEFAULT '0',
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `bio` text,
  `abstract` text,
  `image_file` varchar(255) DEFAULT NULL,
  `abstract_file` varchar(255) DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `accepted` binary(1) NOT NULL DEFAULT '0',
  `conference_id` varchar(16) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conference_registrations`
--

DROP TABLE IF EXISTS `conference_registrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conference_registrations` (
  `id` varchar(16) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `salutation` varchar(10) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `organization` varchar(150) DEFAULT NULL,
  `registration_type` varchar(30) DEFAULT NULL,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `conference_id` varchar(16) NOT NULL DEFAULT '0',
  `receive_email_updates` binary(1) NOT NULL DEFAULT '1',
  `address` varchar(150) DEFAULT NULL,
  `address2` varchar(150) DEFAULT NULL,
  `city` varchar(150) DEFAULT NULL,
  `state` varchar(150) DEFAULT NULL,
  `zip` varchar(15) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `attended` binary(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conferences`
--

DROP TABLE IF EXISTS `conferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conferences` (
  `id` varchar(16) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `short_title` varchar(15) DEFAULT NULL,
  `url` text,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `description` text,
  `location` text,
  `registration_table` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_list`
--

DROP TABLE IF EXISTS `contact_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_list` (
  `contact_id` int(6) NOT NULL AUTO_INCREMENT,
  `last_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `affiliation` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `unit` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `type` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `type_id` int(4) DEFAULT NULL,
  `location` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `pos_category` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `notes` text CHARACTER SET latin1,
  `jobtitle` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `full_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `webpage` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `email_ok` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `source` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `addr1` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `addr2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `addr3` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `city` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `state` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `zip` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `country` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `cell` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `pager` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=MyISAM AUTO_INCREMENT=840 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `courses` (
  `courses_id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `number` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `description` text,
  `URL` varchar(255) DEFAULT NULL,
  `instructor_id` int(4) DEFAULT NULL,
  `instructor` varchar(255) DEFAULT NULL,
  `instructor_url` varchar(255) DEFAULT NULL,
  `cross-list_number` varchar(255) DEFAULT NULL,
  `nano_impact` text,
  PRIMARY KEY (`courses_id`),
  KEY `name` (`name`),
  KEY `department` (`department`),
  KEY `instructor` (`instructor`),
  KEY `instructor_id` (`instructor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `creb_2016_reg`
--

DROP TABLE IF EXISTS `creb_2016_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `creb_2016_reg` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `payment_amount` float DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  `paypal_post_data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=205 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `creb_2017_reg`
--

DROP TABLE IF EXISTS `creb_2017_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `creb_2017_reg` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `payment_amount` float NOT NULL DEFAULT '0',
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  `paypal_post_data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `current_aimlab_users_updated`
--

DROP TABLE IF EXISTS `current_aimlab_users_updated`;
/*!50001 DROP VIEW IF EXISTS `current_aimlab_users_updated`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `current_aimlab_users_updated` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `last_login`,
 1 AS `receive_announcements`,
 1 AS `deleted`*/;
SET character_set_client = @saved_cs_client;

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
-- Temporary table structure for view `current_fablab_users`
--

DROP TABLE IF EXISTS `current_fablab_users`;
/*!50001 DROP VIEW IF EXISTS `current_fablab_users`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `current_fablab_users` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `last_login`,
 1 AS `receive_announcements`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `current_fablab_users_updated`
--

DROP TABLE IF EXISTS `current_fablab_users_updated`;
/*!50001 DROP VIEW IF EXISTS `current_fablab_users_updated`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `current_fablab_users_updated` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `last_login`,
 1 AS `receive_announcements`,
 1 AS `deleted`*/;
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
-- Temporary table structure for view `current_nisplab_resource_users`
--

DROP TABLE IF EXISTS `current_nisplab_resource_users`;
/*!50001 DROP VIEW IF EXISTS `current_nisplab_resource_users`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `current_nisplab_resource_users` AS SELECT 
 1 AS `machid`,
 1 AS `user_id`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `current_nisplab_users`
--

DROP TABLE IF EXISTS `current_nisplab_users`;
/*!50001 DROP VIEW IF EXISTS `current_nisplab_users`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `current_nisplab_users` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `last_login`,
 1 AS `receive_announcements`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `current_roboticslab_users_updated`
--

DROP TABLE IF EXISTS `current_roboticslab_users_updated`;
/*!50001 DROP VIEW IF EXISTS `current_roboticslab_users_updated`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `current_roboticslab_users_updated` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `last_login`,
 1 AS `receive_announcements`,
 1 AS `deleted`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `current_saclab_users`
--

DROP TABLE IF EXISTS `current_saclab_users`;
/*!50001 DROP VIEW IF EXISTS `current_saclab_users`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `current_saclab_users` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `user_type`,
 1 AS `advisor`,
 1 AS `advisor_email`,
 1 AS `last_login`,
 1 AS `receive_announcements`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dead_emails`
--

DROP TABLE IF EXISTS `dead_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dead_emails` (
  `email` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department` (
  `department_id` int(4) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `college` varchar(75) DEFAULT NULL,
  `description` text,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `efrc_all_users`
--

DROP TABLE IF EXISTS `efrc_all_users`;
/*!50001 DROP VIEW IF EXISTS `efrc_all_users`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `efrc_all_users` AS SELECT 
 1 AS `last_name`,
 1 AS `first_name`,
 1 AS `organization`,
 1 AS `email`,
 1 AS `user_id`,
 1 AS `efrc_role`,
 1 AS `efrc_thrust`,
 1 AS `efrc_approved`,
 1 AS `former_member`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `efrc_current_users`
--

DROP TABLE IF EXISTS `efrc_current_users`;
/*!50001 DROP VIEW IF EXISTS `efrc_current_users`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `efrc_current_users` AS SELECT 
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `efrc_role`,
 1 AS `efrc_thrust`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `efrc_teleseminars`
--

DROP TABLE IF EXISTS `efrc_teleseminars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `efrc_teleseminars` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `date_time` timestamp NULL DEFAULT NULL,
  `location` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `abstract` text CHARACTER SET latin1,
  `abstract_filename` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `speaker` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `speaker_bio` text CHARACTER SET latin1,
  `view_instructions` text CHARACTER SET latin1,
  `slides_filename` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `folder` text CHARACTER SET latin1,
  `web_link` text CHARACTER SET latin1,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=151 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `efrc_users`
--

DROP TABLE IF EXISTS `efrc_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `efrc_users` (
  `user_id` int(4) NOT NULL,
  `efrc_approved` binary(1) DEFAULT '0',
  `efrc_role` varchar(40) DEFAULT 'None',
  `efrc_thrust` varchar(30) DEFAULT 'None',
  `efrc_support` varchar(30) DEFAULT 'None',
  `efrc_supervisor` varchar(50) DEFAULT NULL,
  `efrc_duties` text,
  `efrc_start_date` date DEFAULT NULL,
  `efrc_end_date` date DEFAULT NULL,
  `efrc_degs_recd` text,
  `efrc_appts_following` text,
  `efrc_appt_letter_sent` binary(1) DEFAULT NULL,
  `former_member` binary(1) DEFAULT '0',
  `efrc_admin` binary(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipment` (
  `equipment_id` int(4) NOT NULL AUTO_INCREMENT,
  `submit_id` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `nickname` varchar(15) CHARACTER SET latin1 DEFAULT NULL,
  `name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `umd_id` int(5) DEFAULT '0',
  `serial_id` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `model` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `usage` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `description` text CHARACTER SET latin1,
  `quality` int(1) DEFAULT '0',
  `value` int(7) DEFAULT '0',
  `width` int(3) DEFAULT '0',
  `height` int(3) DEFAULT '0',
  `depth` int(3) DEFAULT '0',
  `new` varchar(5) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `access_level` int(2) DEFAULT '0',
  `comments` text CHARACTER SET latin1,
  `size` varchar(15) CHARACTER SET latin1 DEFAULT NULL,
  `visibility` int(2) DEFAULT '0',
  `category` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `type` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `manufacturer` varchar(40) CHARACTER SET latin1 DEFAULT NULL,
  `owner` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `lab_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `lab_rm_building` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `machid` varchar(16) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `lab_id` int(4) DEFAULT NULL,
  `status` char(1) CHARACTER SET latin1 NOT NULL DEFAULT 'a',
  `minRes` int(11) NOT NULL DEFAULT '0',
  `maxRes` int(11) NOT NULL DEFAULT '0',
  `autoAssign` smallint(1) DEFAULT NULL,
  `approval` smallint(1) DEFAULT NULL,
  `allow_multi` smallint(1) DEFAULT NULL,
  `rphone` varchar(16) CHARACTER SET latin1 DEFAULT NULL,
  `location` varchar(250) CHARACTER SET latin1 DEFAULT NULL,
  `notes` text CHARACTER SET latin1,
  `cnam` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`equipment_id`),
  KEY `rs_machid` (`machid`),
  KEY `rs_name` (`name`),
  KEY `rs_status` (`status`),
  KEY `rs_scheduleid` (`lab_id`),
  KEY `type` (`type`),
  KEY `visibility` (`visibility`)
) ENGINE=MyISAM AUTO_INCREMENT=245 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `equipment_location`
--

DROP TABLE IF EXISTS `equipment_location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipment_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `lab_id` int(4) DEFAULT NULL,
  `lab_name` varchar(75) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `executive_user_types`
--

DROP TABLE IF EXISTS `executive_user_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `executive_user_types` (
  `executive_user_type_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  PRIMARY KEY (`executive_user_type_id`),
  KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='User attribute to distinguish execs';
/*!40101 SET character_set_client = @saved_cs_client */;

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
-- Table structure for table `fqm_2017_reg`
--

DROP TABLE IF EXISTS `fqm_2017_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fqm_2017_reg` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `payment_amount` float NOT NULL DEFAULT '0',
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  `paypal_post_data` text,
  `country` varchar(255) DEFAULT NULL,
  `sa_id` varchar(32) DEFAULT NULL,
  `check_in_timestamp` timestamp NULL DEFAULT NULL,
  `sunday` int(1) NOT NULL DEFAULT '0',
  `monday` int(1) NOT NULL DEFAULT '0',
  `tuesday` int(1) NOT NULL DEFAULT '0',
  `wednesday` int(1) NOT NULL DEFAULT '0',
  `thursday` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fqm_2017_school_applications`
--

DROP TABLE IF EXISTS `fqm_2017_school_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fqm_2017_school_applications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL COMMENT 'grad student, post doc, professor, etc.',
  `reason_for_attending` text,
  `keywords` text,
  `status` varchar(11) DEFAULT 'Undecided' COMMENT '0 = unreviewed, 1 = accepted, 2 = waitlist',
  `last_updated` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `submit_date` datetime DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(64) DEFAULT NULL,
  `zip` varchar(32) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `advisors` varchar(255) DEFAULT NULL,
  `sa_id` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `frs_pi_list`
--

DROP TABLE IF EXISTS `frs_pi_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `frs_pi_list` (
  `frs` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `last_name` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `first_name` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `user_id` int(4) DEFAULT NULL,
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `account_id` int(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `frs_user_list`
--

DROP TABLE IF EXISTS `frs_user_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `frs_user_list` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `user_id` int(4) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `account_id` int(4) DEFAULT NULL,
  `frs` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=213 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `highlight_topic_link`
--

DROP TABLE IF EXISTS `highlight_topic_link`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `highlight_topic_link` (
  `highlight_id` int(4) NOT NULL,
  `topic_id` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `highlight_topics`
--

DROP TABLE IF EXISTS `highlight_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `highlight_topics` (
  `id` int(2) unsigned NOT NULL AUTO_INCREMENT,
  `topic` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `highlights`
--

DROP TABLE IF EXISTS `highlights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `highlights` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `text` text COMMENT 'html friendly, please close all tags!',
  `keywords` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'relative path to image on website',
  `caption` text,
  `datestamp` date DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `submitter` varchar(100) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `publish` int(1) DEFAULT '0',
  `movie_link` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labs`
--

DROP TABLE IF EXISTS `labs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `archived` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`lab_id`),
  KEY `name` (`labTitle`),
  KEY `manager` (`manager`)
) ENGINE=MyISAM AUTO_INCREMENT=107 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `march_for_science_email_list`
--

DROP TABLE IF EXISTS `march_for_science_email_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `march_for_science_email_list` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlmr_2016_reg`
--

DROP TABLE IF EXISTS `mlmr_2016_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlmr_2016_reg` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `payment_amount` float DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=130 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlmr_2017_reg`
--

DROP TABLE IF EXISTS `mlmr_2017_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlmr_2017_reg` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `payment_amount` float DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlmr_2017_reg_copy`
--

DROP TABLE IF EXISTS `mlmr_2017_reg_copy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlmr_2017_reg_copy` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `payment_amount` float DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlmr_2017_reg_copy2`
--

DROP TABLE IF EXISTS `mlmr_2017_reg_copy2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlmr_2017_reg_copy2` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `payment_amount` float DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlmr_2018_reg`
--

DROP TABLE IF EXISTS `mlmr_2018_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlmr_2018_reg` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `payment_amount` float DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  `presenting_poster` varchar(16) NOT NULL DEFAULT 'Not sure',
  `attending_workshop` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=158 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mlmr_2018_reg_copy`
--

DROP TABLE IF EXISTS `mlmr_2018_reg_copy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlmr_2018_reg_copy` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `payment_amount` float DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  `presenting_poster` varchar(16) NOT NULL DEFAULT 'Not sure',
  `attending_workshop` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=158 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nano_minor_courses`
--

DROP TABLE IF EXISTS `nano_minor_courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nano_minor_courses` (
  `course_id` int(4) NOT NULL AUTO_INCREMENT,
  `course_listing` varchar(30) DEFAULT NULL,
  `course_title` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `instructor` varchar(255) DEFAULT NULL,
  `char` binary(1) DEFAULT '0',
  `fab` binary(1) DEFAULT '0',
  `app` binary(1) DEFAULT '0',
  `fund` binary(1) DEFAULT '0',
  `notes` text,
  PRIMARY KEY (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nanocenter_staff`
--

DROP TABLE IF EXISTS `nanocenter_staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nanocenter_staff` (
  `user_id` int(11) unsigned NOT NULL,
  `section` varchar(30) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nanoday_2014_mailing_list`
--

DROP TABLE IF EXISTS `nanoday_2014_mailing_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nanoday_2014_mailing_list` (
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nanoday_2014_posters`
--

DROP TABLE IF EXISTS `nanoday_2014_posters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nanoday_2014_posters` (
  `poster_id` int(4) NOT NULL AUTO_INCREMENT,
  `poster_title` varchar(255) DEFAULT NULL,
  `poster_authors` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `funding` text,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(75) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `university` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `presented` tinyint(1) DEFAULT '0',
  `registered` tinyint(1) DEFAULT '0',
  `advisor` varchar(255) DEFAULT NULL,
  `advisor_id` int(4) DEFAULT NULL,
  `poster_summary` text,
  `poster_category_1` varchar(100) DEFAULT NULL,
  `poster_category_2` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`poster_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nanoday_2014_posters_final`
--

DROP TABLE IF EXISTS `nanoday_2014_posters_final`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nanoday_2014_posters_final` (
  `poster_id` int(4) NOT NULL AUTO_INCREMENT,
  `poster_title` varchar(255) DEFAULT NULL,
  `poster_authors` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `funding` text,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(75) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `university` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `presented` tinyint(1) DEFAULT '0',
  `registered` tinyint(1) DEFAULT '0',
  `advisor` varchar(255) DEFAULT NULL,
  `advisor_id` int(4) DEFAULT NULL,
  `poster_summary` text,
  `poster_category_1` varchar(100) DEFAULT NULL,
  `poster_category_2` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`poster_id`)
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nanoday_2014_reg`
--

DROP TABLE IF EXISTS `nanoday_2014_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nanoday_2014_reg` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `registration_type` varchar(50) DEFAULT NULL,
  `work_address` varchar(255) DEFAULT NULL,
  `work_address2` varchar(255) DEFAULT NULL,
  `work_city` varchar(255) DEFAULT NULL,
  `work_state` varchar(45) DEFAULT NULL,
  `work_zip` varchar(20) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '1',
  `registerd_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  `workshop_1` binary(1) DEFAULT '0',
  `workshop_2` binary(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=326 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nanoday_2016_mailing_list`
--

DROP TABLE IF EXISTS `nanoday_2016_mailing_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nanoday_2016_mailing_list` (
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nanoday_2016_posters`
--

DROP TABLE IF EXISTS `nanoday_2016_posters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nanoday_2016_posters` (
  `poster_id` int(4) NOT NULL AUTO_INCREMENT,
  `poster_title` varchar(255) DEFAULT NULL,
  `poster_authors` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `funding` text,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(75) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `university` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `presented` tinyint(1) DEFAULT '0',
  `registered` tinyint(1) DEFAULT '0',
  `advisor` varchar(255) DEFAULT NULL,
  `advisor_id` int(4) DEFAULT NULL,
  `poster_summary` text,
  `poster_category_1` varchar(100) DEFAULT NULL,
  `poster_category_2` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`poster_id`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nanoday_2016_reg`
--

DROP TABLE IF EXISTS `nanoday_2016_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nanoday_2016_reg` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `registration_type` varchar(50) DEFAULT NULL,
  `work_address` varchar(255) DEFAULT NULL,
  `work_address2` varchar(255) DEFAULT NULL,
  `work_city` varchar(255) DEFAULT NULL,
  `work_state` varchar(45) DEFAULT NULL,
  `work_zip` varchar(20) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '1',
  `registerd_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  `workshop_1` binary(1) DEFAULT '0',
  `workshop_2` binary(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nc_users`
--

DROP TABLE IF EXISTS `nc_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nc_users` (
  `user_id` int(11) DEFAULT NULL,
  `umd_uid` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `salutation` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `rank` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `cell_phone` varchar(255) DEFAULT NULL,
  `home_phone` varchar(255) DEFAULT NULL,
  `work_title` varchar(255) DEFAULT NULL,
  `work_phone` varchar(255) DEFAULT NULL,
  `work_address` varchar(255) DEFAULT NULL,
  `work_address2` varchar(255) DEFAULT NULL,
  `work_city` varchar(255) DEFAULT NULL,
  `work_state` varchar(255) DEFAULT NULL,
  `work_country` varchar(255) DEFAULT NULL,
  `work_zip` double DEFAULT NULL,
  `lab_id` varchar(255) DEFAULT NULL,
  `timestamp_added` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `university` varchar(255) DEFAULT NULL,
  `biography` text,
  `affiliations` varchar(255) DEFAULT NULL,
  `visibility` double DEFAULT NULL,
  `webpage` varchar(255) DEFAULT NULL,
  `group_site` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `advisor` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `p/s` varchar(255) DEFAULT NULL,
  `type_id` double DEFAULT NULL,
  `exec_type_id` double DEFAULT NULL,
  `research_interests` text,
  `password` varchar(255) DEFAULT NULL,
  `rights` double DEFAULT NULL,
  `department_id` double DEFAULT NULL,
  `relationship` varchar(255) DEFAULT NULL,
  `supervisor` varchar(255) DEFAULT NULL,
  `register_status` double DEFAULT NULL,
  `intranet_access` double DEFAULT NULL,
  `receive_announcements` double DEFAULT NULL,
  `publish_email_on_site` double DEFAULT NULL,
  `is_collaborator` double DEFAULT NULL,
  `collaboration_info` varchar(255) DEFAULT NULL,
  `login_count` double DEFAULT NULL,
  `memberid` varchar(255) DEFAULT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `e_add` varchar(255) DEFAULT NULL,
  `e_mod` varchar(255) DEFAULT NULL,
  `e_del` varchar(255) DEFAULT NULL,
  `e_app` varchar(255) DEFAULT NULL,
  `e_html` varchar(255) DEFAULT NULL,
  `lab_pref` varchar(255) DEFAULT NULL,
  `logon_name` varchar(255) DEFAULT NULL,
  `is_admin` double DEFAULT NULL,
  `cnst_member` double DEFAULT NULL,
  `nc_member` double DEFAULT NULL,
  `remote_ip` varchar(255) DEFAULT NULL,
  `deleted` double DEFAULT NULL,
  `register_site` varchar(255) DEFAULT NULL,
  `researcher_id` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `new_users`
--

DROP TABLE IF EXISTS `new_users`;
/*!50001 DROP VIEW IF EXISTS `new_users`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `new_users` AS SELECT 
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `email`,
 1 AS `organization`,
 1 AS `rank`,
 1 AS `title`,
 1 AS `last_login`,
 1 AS `login_count`,
 1 AS `timestamp_added`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `number_of_logins_per_user`
--

DROP TABLE IF EXISTS `number_of_logins_per_user`;
/*!50001 DROP VIEW IF EXISTS `number_of_logins_per_user`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `number_of_logins_per_user` AS SELECT 
 1 AS `user_id`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `login_count`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `partners`
--

DROP TABLE IF EXISTS `partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `partners` (
  `partners_id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `description` text,
  `location` varchar(255) DEFAULT NULL,
  `priority` int(1) NOT NULL DEFAULT '1',
  `url` varchar(255) DEFAULT NULL,
  `director` varchar(255) DEFAULT NULL,
  `visible` char(3) NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`partners_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patents`
--

DROP TABLE IF EXISTS `patents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `user_id` int(4) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `applicants` text,
  `patent_num` varchar(12) DEFAULT NULL,
  KEY `id` (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posters`
--

DROP TABLE IF EXISTS `posters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `posters` (
  `poster_id` int(4) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `department` int(10) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(255) DEFAULT NULL,
  `submitted_by` int(4) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `funding` text,
  PRIMARY KEY (`poster_id`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `previous_month_reservations`
--

DROP TABLE IF EXISTS `previous_month_reservations`;
/*!50001 DROP VIEW IF EXISTS `previous_month_reservations`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `previous_month_reservations` AS SELECT 
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
 1 AS `pi_nc_member`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `previous_month_reservations_2`
--

DROP TABLE IF EXISTS `previous_month_reservations_2`;
/*!50001 DROP VIEW IF EXISTS `previous_month_reservations_2`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `previous_month_reservations_2` AS SELECT 
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
 1 AS `account_category`*/;
SET character_set_client = @saved_cs_client;

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
-- Table structure for table `publications`
--

DROP TABLE IF EXISTS `publications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `publications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `authors` text,
  `title` text,
  `status` varchar(20) DEFAULT NULL,
  `pub_date` date DEFAULT NULL,
  `journal_title` varchar(100) DEFAULT NULL,
  `journal_volume` varchar(20) DEFAULT NULL,
  `journal_issue` varchar(20) DEFAULT NULL,
  `journal_page_range` varchar(20) DEFAULT NULL,
  `abstract` text,
  `pub_filename` text,
  `public` tinyint(1) DEFAULT '0',
  `support_fablab` tinyint(1) DEFAULT '0',
  `support_nisplab` tinyint(1) DEFAULT '0',
  `support_mrsec` tinyint(1) DEFAULT '0',
  `support_nsf` tinyint(1) DEFAULT '0',
  `support_other` text,
  `support_nc` tinyint(1) DEFAULT '0',
  `support_lps` tinyint(1) DEFAULT '0',
  `support_nees` tinyint(1) DEFAULT '0',
  `citation` text,
  `doi` varchar(100) DEFAULT NULL,
  `categories` varchar(50) DEFAULT NULL,
  `show_nc_site` tinyint(1) DEFAULT '0',
  `show_efrc_site` tinyint(1) DEFAULT '0',
  `highlight_id` int(3) unsigned DEFAULT NULL,
  `accepted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1889 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=3377 DEFAULT CHARSET=utf8;
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
  `operational_status` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`machid`),
  KEY `rs_machid` (`machid`),
  KEY `rs_scheduleid` (`lab_id`),
  KEY `rs_name` (`name`),
  KEY `rs_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(32) NOT NULL DEFAULT '',
  `access` int(11) unsigned DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `total_number_of_nc_logins`
--

DROP TABLE IF EXISTS `total_number_of_nc_logins`;
/*!50001 DROP VIEW IF EXISTS `total_number_of_nc_logins`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `total_number_of_nc_logins` AS SELECT 
 1 AS `total`*/;
SET character_set_client = @saved_cs_client;

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
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=114 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=17083 DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wbg_2014_abstracts`
--

DROP TABLE IF EXISTS `wbg_2014_abstracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wbg_2014_abstracts` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `bio` text,
  `abstract` text,
  `image_file` varchar(255) DEFAULT NULL,
  `abstract_file` varchar(255) DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `accepted` binary(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wbg_2014_reg`
--

DROP TABLE IF EXISTS `wbg_2014_reg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wbg_2014_reg` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `invoice` varchar(50) DEFAULT NULL,
  `txn_id` varchar(50) DEFAULT NULL,
  `salutation` varchar(5) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `reg_type` varchar(50) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(45) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `rec_email` tinyint(1) NOT NULL DEFAULT '0',
  `register_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `attended` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=149 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `EFRC Former Members`
--

/*!50001 DROP VIEW IF EXISTS `EFRC Former Members`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `EFRC Former Members` AS select `u`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`eu`.`efrc_thrust` AS `efrc_thrust`,`eu`.`efrc_role` AS `efrc_role`,`eu`.`former_member` AS `former_member` from (`efrc_users` `eu` join `user` `u` on((`eu`.`user_id` = `u`.`user_id`))) where ((`eu`.`former_member` = 1) and ((`eu`.`efrc_role` = 'Associate Director for Programs') or (`eu`.`efrc_role` = 'Thrust Leader/co-leader') or (`eu`.`efrc_role` = 'Faculty/Senior Researcher') or (`eu`.`efrc_role` = 'External Advisory Board') or (`eu`.`efrc_role` = 'Postdoc') or (`eu`.`efrc_role` = 'Grad Student') or (`eu`.`efrc_role` = 'Undergrad Student'))) order by (case `eu`.`efrc_role` when 'Associate Director for Programs' then 1 when 'Thrust Leader/co-leader' then 2 when 'Faculty/Senior Researcher' then 3 when 'External Advisory Board' then 4 when 'Postdoc' then 5 when 'Grad Student' then 6 when 'Undergrad Student' then 7 when 'None' then 8 end) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `EFRC PostDocs Grads`
--

/*!50001 DROP VIEW IF EXISTS `EFRC PostDocs Grads`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `EFRC PostDocs Grads` AS select `eu`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`eu`.`efrc_role` AS `efrc_role`,`eu`.`efrc_thrust` AS `efrc_thrust`,`eu`.`efrc_approved` AS `efrc_approved`,`eu`.`former_member` AS `former_member` from (`efrc_users` `eu` join `user` `u` on((`eu`.`user_id` = `u`.`user_id`))) where ((`eu`.`efrc_role` like 'Postdoc') or (`eu`.`efrc_role` like 'Grad%')) order by `eu`.`efrc_thrust`,`u`.`last_name`,`u`.`first_name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `admin_accounts`
--

/*!50001 DROP VIEW IF EXISTS `admin_accounts`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `admin_accounts` AS select `a`.`account_id` AS `account_id`,`a`.`FRS` AS `FRS`,`a`.`sub_FRS` AS `sub_FRS`,`a`.`name` AS `name`,`a`.`status` AS `status`,if((`a`.`pi_last_name` = ''),`piu`.`last_name`,`a`.`pi_last_name`) AS `pi_last_name` from (`accounts` `a` left join `user` `piu` on((`a`.`pi` = `piu`.`user_id`))) where (`a`.`archived` = 0) order by `a`.`FRS` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `aimlab_trained_users`
--

/*!50001 DROP VIEW IF EXISTS `aimlab_trained_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `aimlab_trained_users` AS select `l`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`u`.`receive_announcements` AS `receive_announcements`,`u`.`deleted` AS `deleted` from (`lab_permission` `l` join `user` `u` on((`l`.`user_id` = `u`.`user_id`))) where ((`l`.`safety_trained` = 1) and (`l`.`lab_id` = 2) and (`u`.`deleted` = 0)) order by `u`.`last_name`,`u`.`first_name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `all_roboticslab_users_updated`
--

/*!50001 DROP VIEW IF EXISTS `all_roboticslab_users_updated`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `all_roboticslab_users_updated` AS select distinct `u`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`u`.`last_login` AS `last_login`,`u`.`receive_announcements` AS `receive_announcements`,`u`.`deleted` AS `deleted` from ((`permission` `p` join `user` `u` on((`p`.`user_id` = `u`.`user_id`))) join `resources` `r` on((`p`.`machid` = `r`.`machid`))) where ((`r`.`lab_id` = 100) and (`u`.`deleted` = 0)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `current_aimlab_users_updated`
--

/*!50001 DROP VIEW IF EXISTS `current_aimlab_users_updated`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `current_aimlab_users_updated` AS select distinct `ru`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`u`.`last_login` AS `last_login`,`u`.`receive_announcements` AS `receive_announcements`,`u`.`deleted` AS `deleted` from (((`reservations` `r` join `reservation_users` `ru` on((`r`.`resid` = `ru`.`resid`))) join `user` `u` on((`ru`.`user_id` = `u`.`user_id`))) join `resources` `re` on((`re`.`machid` = `r`.`machid`))) where ((from_unixtime(`r`.`start_date`) between (curdate() - interval 60 day) and curdate()) and (`re`.`lab_id` = 2) and (`u`.`deleted` = 0)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `current_fablab_resource_users`
--

/*!50001 DROP VIEW IF EXISTS `current_fablab_resource_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `current_fablab_resource_users` AS select `r`.`machid` AS `machid`,`p`.`user_id` AS `user_id` from (`resources` `r` join `permission` `p` on((`r`.`machid` = `p`.`machid`))) where (`r`.`lab_id` = 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `current_fablab_users`
--

/*!50001 DROP VIEW IF EXISTS `current_fablab_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `current_fablab_users` AS select distinct `c`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`u`.`last_login` AS `last_login`,`u`.`receive_announcements` AS `receive_announcements` from (`current_fablab_resource_users` `c` join `user` `u` on((`c`.`user_id` = `u`.`user_id`))) where (`u`.`last_login` between (curdate() - interval 60 day) and curdate()) order by `u`.`last_login` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `current_fablab_users_updated`
--

/*!50001 DROP VIEW IF EXISTS `current_fablab_users_updated`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `current_fablab_users_updated` AS select distinct `ru`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`u`.`last_login` AS `last_login`,`u`.`receive_announcements` AS `receive_announcements`,`u`.`deleted` AS `deleted` from (((`reservations` `r` join `reservation_users` `ru` on((`r`.`resid` = `ru`.`resid`))) join `user` `u` on((`ru`.`user_id` = `u`.`user_id`))) join `resources` `re` on((`re`.`machid` = `r`.`machid`))) where ((from_unixtime(`r`.`start_date`) between (curdate() - interval 60 day) and curdate()) and (`re`.`lab_id` = 1) and (`u`.`deleted` = 0)) */;
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
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `current_nc_users_updated` AS select distinct `ru`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`u`.`last_login` AS `last_login`,`u`.`receive_announcements` AS `receive_announcements`,`u`.`deleted` AS `deleted` from (((`reservations` `r` join `reservation_users` `ru` on((`r`.`resid` = `ru`.`resid`))) join `user` `u` on((`ru`.`user_id` = `u`.`user_id`))) join `resources` `re` on((`re`.`machid` = `r`.`machid`))) where ((from_unixtime(`r`.`start_date`) between (curdate() - interval 60 day) and curdate()) and (`u`.`deleted` = 0)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `current_nisplab_resource_users`
--

/*!50001 DROP VIEW IF EXISTS `current_nisplab_resource_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `current_nisplab_resource_users` AS select `r`.`machid` AS `machid`,`p`.`user_id` AS `user_id` from (`resources` `r` join `permission` `p` on((`r`.`machid` = `p`.`machid`))) where (`r`.`lab_id` = 2) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `current_nisplab_users`
--

/*!50001 DROP VIEW IF EXISTS `current_nisplab_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `current_nisplab_users` AS select distinct `c`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`u`.`last_login` AS `last_login`,`u`.`receive_announcements` AS `receive_announcements` from (`current_nisplab_resource_users` `c` join `user` `u` on((`c`.`user_id` = `u`.`user_id`))) where (`u`.`last_login` between (curdate() - interval 60 day) and curdate()) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `current_roboticslab_users_updated`
--

/*!50001 DROP VIEW IF EXISTS `current_roboticslab_users_updated`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `current_roboticslab_users_updated` AS select distinct `ru`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`u`.`last_login` AS `last_login`,`u`.`receive_announcements` AS `receive_announcements`,`u`.`deleted` AS `deleted` from (((`reservations` `r` join `reservation_users` `ru` on((`r`.`resid` = `ru`.`resid`))) join `user` `u` on((`ru`.`user_id` = `u`.`user_id`))) join `resources` `re` on((`re`.`machid` = `r`.`machid`))) where ((from_unixtime(`r`.`start_date`) between (curdate() - interval 60 day) and curdate()) and (`re`.`lab_id` = 100) and (`u`.`deleted` = 0)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `current_saclab_users`
--

/*!50001 DROP VIEW IF EXISTS `current_saclab_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `current_saclab_users` AS select distinct `ru`.`user_id` AS `user_id`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`ut`.`title` AS `user_type`,concat(`ua`.`first_name`,' ',`ua`.`last_name`) AS `advisor`,`ua`.`email` AS `advisor_email`,`u`.`last_login` AS `last_login`,`u`.`receive_announcements` AS `receive_announcements` from (((((`reservations` `r` join `reservation_users` `ru` on((`r`.`resid` = `ru`.`resid`))) join `user` `u` on((`ru`.`user_id` = `u`.`user_id`))) join `resources` `re` on((`re`.`machid` = `r`.`machid`))) join `user_type` `ut` on((`u`.`type_id` = `ut`.`user_type_id`))) left join `user` `ua` on((`u`.`advisor` = `ua`.`user_id`))) where ((from_unixtime(`r`.`start_date`) between (curdate() - interval 1 year) and curdate()) and (`re`.`lab_id` = 87)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `efrc_all_users`
--

/*!50001 DROP VIEW IF EXISTS `efrc_all_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `efrc_all_users` AS select `u`.`last_name` AS `last_name`,`u`.`first_name` AS `first_name`,`u`.`organization` AS `organization`,`u`.`email` AS `email`,`e`.`user_id` AS `user_id`,`e`.`efrc_role` AS `efrc_role`,`e`.`efrc_thrust` AS `efrc_thrust`,`e`.`efrc_approved` AS `efrc_approved`,`e`.`former_member` AS `former_member` from (`efrc_users` `e` join `user` `u` on((`e`.`user_id` = `u`.`user_id`))) order by `u`.`last_name`,`u`.`first_name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `efrc_current_users`
--

/*!50001 DROP VIEW IF EXISTS `efrc_current_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `efrc_current_users` AS select `u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`eu`.`efrc_role` AS `efrc_role`,`eu`.`efrc_thrust` AS `efrc_thrust` from (`efrc_users` `eu` join `user` `u` on((`eu`.`user_id` = `u`.`user_id`))) where ((`eu`.`former_member` = 0) and (`eu`.`efrc_approved` = 1)) */;
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
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `fablab_equipment_fees_contact` AS select `resources`.`machid` AS `machid`,`resources`.`short_name` AS `short_name`,`resources`.`name` AS `name`,`resources`.`category` AS `category`,`resources`.`umd_rate` AS `umd_rate`,`resources`.`nc_member_rate` AS `nc_member_rate`,`resources`.`maryland_system_rate` AS `maryland_system_rate`,`resources`.`university_rate` AS `university_rate`,`resources`.`government_rate` AS `government_rate`,`resources`.`industry_rate` AS `industry_rate`,`resources`.`staff_contact` AS `staff_contact` from `resources` where (`resources`.`lab_id` = 1) order by `resources`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `new_users`
--

/*!50001 DROP VIEW IF EXISTS `new_users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `new_users` AS select `u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `email`,`u`.`organization` AS `organization`,`u`.`rank` AS `rank`,`ut`.`title` AS `title`,`u`.`last_login` AS `last_login`,`u`.`login_count` AS `login_count`,`u`.`timestamp_added` AS `timestamp_added` from (`user` `u` join `user_type` `ut` on((`u`.`type_id` = `ut`.`user_type_id`))) where (timestampdiff(MONTH,`u`.`timestamp_added`,now()) < 3) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `number_of_logins_per_user`
--

/*!50001 DROP VIEW IF EXISTS `number_of_logins_per_user`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `number_of_logins_per_user` AS select `user`.`user_id` AS `user_id`,`user`.`first_name` AS `first_name`,`user`.`last_name` AS `last_name`,`user`.`login_count` AS `login_count` from `user` where (`user`.`login_count` > 0) order by `user`.`login_count` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `previous_month_reservations`
--

/*!50001 DROP VIEW IF EXISTS `previous_month_reservations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `previous_month_reservations` AS select date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8'JIS')) AS `date`,date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(TIME, _utf8'JIS')) AS `start`,date_format(from_unixtime((`r`.`end_date` + (60 * `r`.`endTime`))),get_format(TIME, _utf8'JIS')) AS `end`,((time_to_sec(timediff(date_format(from_unixtime((`r`.`endTime` * 60)),get_format(TIME, _utf8'JIS')),date_format(from_unixtime((`r`.`startTime` * 60)),get_format(TIME, _utf8'JIS')))) / 60) / 60) AS `hours`,`resources`.`name` AS `name`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `user_email`,`ru`.`user_id` AS `user_id`,`r`.`resid` AS `resid`,`r`.`summary` AS `summary`,`r`.`account_id` AS `account_id`,`r`.`technical_note` AS `technical_note`,`r`.`billing_note` AS `billing_note`,`r`.`machid` AS `resource_id`,`labs`.`nickname` AS `lab`,`resources`.`lab_id` AS `lab_id`,`a`.`FRS` AS `FRS`,`a`.`fed_id` AS `fed_id`,`a`.`pi` AS `pi`,`piu`.`first_name` AS `PI First Name`,`piu`.`last_name` AS `PI Last Name`,`a`.`pi_first_name` AS `pi_first_name`,`a`.`pi_last_name` AS `pi_last_name`,if(isnull(`a`.`pi`),`a`.`pi_last_name`,`piu`.`last_name`) AS `Pivot Table Label`,if((`piu`.`nc_member` <> _utf8'0'),1,0) AS `pi_nc_member` from (((((((`reservations` `r` join `resources` on((`r`.`machid` = `resources`.`machid`))) join `reservation_users` `ru` on((`r`.`resid` = `ru`.`resid`))) join `user` `u` on((`ru`.`user_id` = `u`.`user_id`))) join `labs` on((`resources`.`lab_id` = `labs`.`lab_id`))) left join `user` `ua` on((`u`.`advisor` = `ua`.`user_id`))) left join `accounts` `a` on((`r`.`account_id` = `a`.`account_id`))) left join `user` `piu` on((`a`.`pi` = `piu`.`user_id`))) where ((date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8'JIS')) between date_format((now() - interval 1 month),_utf8'%Y-%m-16') and date_format(now(),_utf8'%Y-%m-15')) and (`r`.`deleted` = 0)) order by date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8'JIS')),`a`.`pi_last_name`,`a`.`pi_first_name`,`u`.`last_name`,`u`.`first_name`,`resources`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `previous_month_reservations_2`
--

/*!50001 DROP VIEW IF EXISTS `previous_month_reservations_2`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `previous_month_reservations_2` AS select date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8'JIS')) AS `date`,date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(TIME, _utf8'JIS')) AS `start`,date_format(from_unixtime((`r`.`end_date` + (60 * `r`.`endTime`))),get_format(TIME, _utf8'JIS')) AS `end`,((time_to_sec(timediff(date_format(from_unixtime((`r`.`endTime` * 60)),get_format(TIME, _utf8'JIS')),date_format(from_unixtime((`r`.`startTime` * 60)),get_format(TIME, _utf8'JIS')))) / 60) / 60) AS `hours`,`resources`.`name` AS `name`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `user_email`,`ru`.`user_id` AS `user_id`,`r`.`resid` AS `resid`,`r`.`summary` AS `summary`,`r`.`account_id` AS `account_id`,`r`.`technical_note` AS `technical_note`,`r`.`billing_note` AS `billing_note`,`r`.`machid` AS `resource_id`,`labs`.`nickname` AS `lab`,`resources`.`lab_id` AS `lab_id`,`a`.`FRS` AS `FRS`,`a`.`fed_id` AS `fed_id`,`a`.`pi` AS `pi`,`piu`.`first_name` AS `PI First Name`,`piu`.`last_name` AS `PI Last Name`,`a`.`pi_first_name` AS `pi_first_name`,`a`.`pi_last_name` AS `pi_last_name`,if(isnull(`a`.`pi`),`a`.`pi_last_name`,`piu`.`last_name`) AS `Pivot Table Label`,if((`piu`.`nc_member` <> _utf8'0'),1,0) AS `pi_nc_member`,`a`.`account_category` AS `account_category` from (((((((`reservations` `r` join `resources` on((`r`.`machid` = `resources`.`machid`))) join `reservation_users` `ru` on((`r`.`resid` = `ru`.`resid`))) join `user` `u` on((`ru`.`user_id` = `u`.`user_id`))) join `labs` on((`resources`.`lab_id` = `labs`.`lab_id`))) left join `user` `ua` on((`u`.`advisor` = `ua`.`user_id`))) left join `accounts` `a` on((`r`.`account_id` = `a`.`account_id`))) left join `user` `piu` on((`a`.`pi` = `piu`.`user_id`))) where ((date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8'JIS')) between date_format((now() - interval 1 month),_utf8'%Y-%m-16') and date_format(now(),_utf8'%Y-%m-15')) and (`r`.`deleted` = 0)) order by date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8'JIS')),`a`.`pi_last_name`,`a`.`pi_first_name`,`u`.`last_name`,`u`.`first_name`,`resources`.`name` */;
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
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `previous_month_reservations_2_rates` AS select date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8'JIS')) AS `date`,date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(TIME, _utf8'JIS')) AS `start`,date_format(from_unixtime((`r`.`end_date` + (60 * `r`.`endTime`))),get_format(TIME, _utf8'JIS')) AS `end`,((time_to_sec(timediff(date_format(from_unixtime((`r`.`endTime` * 60)),get_format(TIME, _utf8'JIS')),date_format(from_unixtime((`r`.`startTime` * 60)),get_format(TIME, _utf8'JIS')))) / 60) / 60) AS `hours`,`resources`.`name` AS `name`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`u`.`email` AS `user_email`,`ru`.`user_id` AS `user_id`,`r`.`resid` AS `resid`,`r`.`summary` AS `summary`,`r`.`account_id` AS `account_id`,`r`.`technical_note` AS `technical_note`,`r`.`billing_note` AS `billing_note`,`r`.`machid` AS `resource_id`,`labs`.`nickname` AS `lab`,`resources`.`lab_id` AS `lab_id`,`a`.`FRS` AS `FRS`,`a`.`fed_id` AS `fed_id`,`a`.`pi` AS `pi`,`piu`.`first_name` AS `PI First Name`,`piu`.`last_name` AS `PI Last Name`,`a`.`pi_first_name` AS `pi_first_name`,`a`.`pi_last_name` AS `pi_last_name`,if(isnull(`a`.`pi`),`a`.`pi_last_name`,`piu`.`last_name`) AS `Pivot Table Label`,if((`piu`.`nc_member` <> _utf8'0'),1,0) AS `pi_nc_member`,`a`.`account_type` AS `account_type`,`at`.`label` AS `rate_type`,`rr`.`rate` AS `rate` from (((((((((`reservations` `r` join `resources` on((`r`.`machid` = `resources`.`machid`))) join `reservation_users` `ru` on((`r`.`resid` = `ru`.`resid`))) join `user` `u` on((`ru`.`user_id` = `u`.`user_id`))) join `labs` on((`resources`.`lab_id` = `labs`.`lab_id`))) left join `user` `ua` on((`u`.`advisor` = `ua`.`user_id`))) left join `accounts` `a` on((`r`.`account_id` = `a`.`account_id`))) left join `user` `piu` on((`a`.`pi` = `piu`.`user_id`))) left join `account_types` `at` on((`at`.`id` = `a`.`account_type`))) left join `resource_rates` `rr` on(((`rr`.`resource_id` = `r`.`machid`) and (`rr`.`account_type_id` = `a`.`account_type`)))) where ((date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8'JIS')) between date_format((now() - interval 1 month),_utf8'%Y-%m-16') and date_format(now(),_utf8'%Y-%m-15')) and (`r`.`deleted` = 0)) order by date_format(from_unixtime((`r`.`start_date` + (60 * `r`.`startTime`))),get_format(DATE, _utf8'JIS')),`a`.`pi_last_name`,`a`.`pi_first_name`,`u`.`last_name`,`u`.`first_name`,`resources`.`name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `total_number_of_nc_logins`
--

/*!50001 DROP VIEW IF EXISTS `total_number_of_nc_logins`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `total_number_of_nc_logins` AS select sum(`user`.`login_count`) AS `total` from `user` where (`user`.`login_count` > 0) */;
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

-- Dump completed on 2020-11-12 21:36:00
