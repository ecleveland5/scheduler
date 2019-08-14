<?php
/**
* Setup program for phpScheduleIt
*
* This will allow a user with root database privleges to
* automatically set up the required database and its
* tables.  It will also populate any necessary tables.
*
* It uses PEAR::DB to prepare and execute the queries,
* making them database independent.
*
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 03-14-05
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Base directory of application
*/
@define('BASE_DIR', dirname(__FILE__) . '/..');
/**
* DBEngine class
*/
include_once(BASE_DIR . '/lib/DBEngine.class.php');
/**
* Template class
*/
include_once(BASE_DIR . '/lib/Template.class.php');

@session_start();	// Start the session

$failed = false;

$t = new Template($conf['app']['title'] . translate('Setup'), 1);
$t->printHTMLHeader();
doPrintHeader();

if (checkConfig()) {

	if (isset($_POST['login'])) {
		setVars();
		doLogin();
	}
	else if (isset($_POST['create'])) {
		$db = dbConnect();
		doCreate();
		doFinish();
	}
	else
		doPrintForm();
}

$t->printHTMLFooter();


/**
* Prints html header
* @param none
*/
function doPrintHeader() {
	global $conf;
?>
<p align="center">
<?php CmnFns::print_language_pulldown()?>
</p>
<h3 align="center"><?php echo $conf['app']['name'];?> v<?php echo $conf['app']['version']?></h3>
<?php
}

/**
* Prints out login form
* @param none
*/
function doPrintForm() {
	global $conf;
?>
<h3 align="center"><?php echo translate('Please log into your database')?></h3>
<form name="login" id="login" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
  <table width="60%" border="0" cellspacing="3" cellpadding="0" align="center" style="border: solid 1px #333333; background-color: #fafafa;">
    <tr>
      <td><?php echo translate('Enter database root username')?></td>
      <td><input type="text" name="user" class="textbox" /></td>
    </tr>
    <tr>
      <td><?php echo translate('Enter database root password')?></td>
      <td><input type="password" name="password" class="textbox" /></td>
    </tr>
    <tr>
      <td><input type="submit" name="login" value="<?php echo translate('Login to database')?>" class="button" /></td>
    </tr>
  </table>
  <br />
  <table width="80%" align="center" cellpadding="3" cellspacing="0" border="0" style="font-family: Verdana, Arial; font-size: 12px; background-color: #ffffff; border: solid 1px #DDDDDD">
    <tr>
      <td>
	  <ul>
	  <li><?php echo translate('Root user is not required. Any database user who has permission to create tables is acceptable.')?></li>
	  <li><?php echo translate('This will set up all the necessary databases and tables for phpScheduleIt.')?></li>
	  <li><?php echo translate('It also populates any required tables.')?></li>
	  <?php if ($conf['db']['drop_old']) echo '<li>' . translate('Warning: THIS WILL ERASE ALL DATA IN PREVIOUS phpScheduleIt DATABASES!') . '</li>';?>
	  </ul></td>
    </tr>
  </table>
</form>
<?php
}

/**
* Checks to make sure necessary fields are set in the config file
* @param none
* @return whether all necessary fields are set
*/
function checkConfig() {
	global $conf;
	switch ($conf['db']['dbType']) {	// Check database type
		case 'mysql' :;
		case 'pgsql' :;
		case 'ibase' :;
		case 'msql' :;
		case 'mssql' :;
		case 'oci8' :;
		case 'odbc' :;
		case 'sybase' :;
		case 'ifx' :;
		case 'fbsql' :;
			break;
		default :
			echo translate('Not a valid database type in the config.php file.');
			return false;
			break;
	}

	if (empty($conf['db']['dbUser'])) {		// Check database user
		echo translate('Database user is not set in the config.php file.');
		return false;
	}

	if (empty($conf['db']['dbPass'])) {		// Check database password
		echo translate('Database user password is not set in the config.php file.');
		return false;
	}

	if (empty($conf['db']['dbName'])) {		// Check database name
		echo translate('Database name not set in the config.php file.');
		return false;
	}

	return true;
}

/**
* Verifies that the user entered information and sets up session variables
* @param none
*/
function setVars() {
	$_SESSION['user'] = stripslashes(trim($_POST['user']));
	$_SESSION['password'] = stripslashes(trim($_POST['password']));
}

/**
* Create a connection to the database using user supplied data
* @param none
*/
function doLogin() {
	global $conf;
    // Data Source Name: This is the universal connection string
    // See http://www.pear.php.net/manual/en/package.database.php#package.database.db
    // for more information on DSN
    $dsn = $conf['db']['dbType'] . '://'
			. $_SESSION['user']
			. ':' . $_SESSION['password']
			. '@' . $conf['db']['hostSpec'];

    // Make connection to database
    $db = DB::connect($dsn);

    // If there is an error, print to browser, print to logfile and kill app
    if (DB::isError($db)) {
        die ('Error connecting to database: ' . $db->getMessage() );
    }
	else {
		echo '<h4 align="center">' . translate('Successfully connected as') . ' ' . $_SESSION['user'] . "</h4>\n"
			. "<form name=\"create\" id=\"create\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">\n"
			. "<input type=\"submit\" name=\"create\" value=\"" . translate('Create tables') . "\" class=\"button\" />\n"
			. "</form>\n";
	}
}

/**
* Create and return a connection to the database
* Requires that setVars() has been called by the user
* loggin in
* @param none
*/
function dbConnect() {
	global $conf;
    // Data Source Name: This is the universal connection string
    // See http://www.pear.php.net/manual/en/package.database.php#package.database.db
    // for more information on DSN
    $dsn = $conf['db']['dbType'] . '://'
			. $_SESSION['user']
			. ':' . $_SESSION['password']
			. '@' . $conf['db']['hostSpec'];

    // Make persistant connection to database
    $db = DB::connect($dsn);

    // If there is an error, print to browser, print to logfile and kill app
    if (DB::isError($db)) {
        die ('Error connecting to database: ' . $db->getMessage() );
    }

    return $db;
}


/**
* Create the database and the tables in it
* - Requires an external file with sql commands
* @param none
*/
function doCreate() {
	global $db;
	global $conf;

	$sqls = array (
					// Create new database
					//array ("create database {$conf['db']['dbName']}", 'Creating database'),
					// Select it
					array ("use `".$conf['db']['dbName']."`;", 'Selecting database'),

// Create account users table
array ("CREATE TABLE account_users (
  		account_users_id int(4) NOT NULL AUTO_INCREMENT,
  		account_id int(4) NOT NULL DEFAULT '0',
  		user_id int(4) NOT NULL DEFAULT '0',
  		start_date date DEFAULT NULL,
  		end_date date DEFAULT NULL,
  		status tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=off, 1=on',
  		is_admin binary(1) NOT NULL DEFAULT '0',
  		PRIMARY KEY (`account_users_id`),
  		KEY `user_id` (`user_id`),
  		KEY `account_id` (`account_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;", 'Creating account users table'),

array("CREATE TABLE `accounts` (
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
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;", 'Creating accounts table'),

array("CREATE TABLE `announcements` (
  `announcementid` varchar(16) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `announcement` text CHARACTER SET utf8,
  `number` smallint(3) NOT NULL DEFAULT '0',
  `start_datetime` int(11) DEFAULT NULL,
  `end_datetime` int(11) DEFAULT NULL,
  `lab_id` int(4) DEFAULT NULL,
  PRIMARY KEY (`announcementid`),
  KEY `announcements_startdatetime` (`start_datetime`),
  KEY `announcements_enddatetime` (`end_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;", 'Creating announcements table'),

array("CREATE TABLE `billing_imported` (
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
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;", 'Create billing_imported table'),

array("CREATE TABLE `lab_permission` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;", 'Create lab_permission table'),

array("CREATE TABLE `labs` (
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
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;", 'Create labs table'),

array("CREATE TABLE `permission` (
  `user_id` int(4) NOT NULL DEFAULT '0',
  `machid` char(16) CHARACTER SET utf8 NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`,`machid`),
  KEY `per_memberid` (`user_id`),
  KEY `per_machid` (`machid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;", 'Create permission table'),

array("CREATE TABLE `reservation_users` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;", 'Create reservation_users table'),

array("CREATE TABLE `reservations` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;", 'Create reservations table'),

array("CREATE TABLE `resources` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;", 'Create resources table'),

array("CREATE TABLE `user` (
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
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;", 'Create user table'),

array("CREATE TABLE `user_type` (
  `user_type_id` int(4) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `createAccount` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;", 'Create user_type table')

/*
					// Create announcement table
					array("create table announcements (
					       announcementid varchar(16) not null primary key,
					       announcement varchar(255) not null default '',
					       number smallint(3) not null default '0',
						   start_datetime INT,
  						   end_datetime INT
					       )", 'Creating announcement table'),
					// Create announcements indexes
					array('create index announcements_startdatetime on announcements(start_datetime)', 'Create start_datetime index'),
					array('create index announcements_enddatetime on announcements(end_datetime)', 'Create end_datetime index'),	
					// Create login table
					array ("create table user (
							  user_id char(16) not null primary key,
							  email char(75) not null,
							  password char(32) not null,
							  first_name char(30) not null,
							  last_name char(30) not null,
							  phone char(16) not null,
							  institution char(255),
							  position char(100),
							  e_add char(1) not null default 'y',
							  e_mod char(1) not null default 'y',
							  e_del char(1) not null default 'y',
							  e_app char(1) not null default 'y',
							  e_html char(1) not null default 'y',
							  logon_name char(30),
							  is_admin smallint(1) default 0
							  )", 'Creating user table'),
					// Create user indexes
					array ('create index login_user_id on login (user_id)', 'Creating index'),
					array ('create index login_email on login (email)', 'Creating index'),
					array ('create index login_password on login (password)', 'Creating index'),
					array ('CREATE INDEX login_logonname ON login (logon_name)', 'Creating index'),
					// Create reservations table
					array ('create table reservations (
							  resid char(16) not null primary key,
							  machid char(16) not null,
							  lab_id char(16) not null,
							  start_date int not null default 0,
							  end_date int not null default 0,
							  startTime integer not null,
							  endTime integer not null,
							  created integer not null,
							  modified integer,
							  parentid char(16),
							  is_blackout smallint(1) not null default 0,
							  is_pending smallint(1) not null default 0,
							  summary text
							  )', 'Creating reservations table'),
					// Create reservations indexes
					array ('create index res_resid on reservations (resid)', 'Creating index'),
					array ('create index res_machid on reservations (machid)', 'Creating index'),
					array ('create index res_lab_id on reservations (lab_id)', 'Creating index'),
					array ('create index reservations_startdate on reservations (start_date)', 'Creating index'),
					array ('create index reservations_enddate on reservations (end_date)', 'Creating index'),
					array ('create index res_startTime on reservations (startTime)', 'Creating index'),
					array ('create index res_endTime on reservations (endTime)', 'Creating index'),
					array ('create index res_created on reservations (created)', 'Creating index'),
					array ('create index res_modified on reservations (modified)', 'Creating index'),
					array ('create index res_parentid on reservations (parentid)', 'Creating index'),
					array ('create index res_isblackout on reservations (is_blackout)', 'Creating index'),
					// Create resources table
					array ("create table resources (
							  machid char(16) not null primary key,
							  lab_id char(16) not null,
							  name char(75) not null,
							  location char(250),
							  rphone char(16),
							  notes text,
							  status char(1) not null default 'a',
							  minRes integer not null,
							  maxRes integer not null,
							  autoAssign smallint(1),
							  approval smallint(1),
							  allow_multi smallint(1)
							  )", 'Creating resources table'),
					// Create resources indexes
					array ('create index rs_machid on resources (machid)', 'Creating index'),
					array ('create index rs_lab_id on resources (lab_id)', 'Creating index'),
					array ('create index rs_name on resources (name)', 'Creating index'),
					array ('create index rs_status on resources (status)', 'Creating index'),
					// Create permission table
					array ('create table permission (
							  user_id char(16) not null,
							  machid char(16) not null,
							  primary key(user_id, machid)
							  )', 'Creating permission table'),
					// Create permission indexes
					array ('create index per_user_id on permission (user_id)', 'Creating index'),
					array ('create index per_machid on permission (machid)', 'Creating index'),
					// Create lab table
					array ("create table labs (
							lab_id char(16) not null primary key,
							labTitle char(75),
							dayStart integer not null,
							dayEnd integer not null,
							timeSpan integer not null,
							timeFormat integer not null,
							weekDayStart integer not null,
							viewDays integer not null,
							usePermissions smallint(1),
							isHidden smallint(1),
							showSummary smallint(1),
							adminEmail char(75),
							isDefault smallint(1),
							dayOffset integer
							)", 'Creating table labs'),
					// Create lab indexes
					array ('create index sh_lab_id on labs (lab_id)', 'Creating index'),
					array ('create index sh_hidden on labs (isHidden)', 'Creating index'),
					array ('create index sh_perms on labs (usePermissions)', 'Creating index'),
					// Create lab permission tables
					array ("create table lab_permission (
							lab_id char(16) not null,
							user_id char(16) not null,
							primary key(lab_id, user_id)
							)", 'Creating table lab_permission'),
					// Create lab permission indexes
					array ('create index sp_lab_id on lab_permission (lab_id)', 'Creating index'),
					array ('create index sp_user_id on lab_permission (user_id)', 'Creating index'),
					// Create reservation/user association table
					array ('create table reservation_users (
						  resid char(16) not null,
						  user_id char(16) not null,
						  owner smallint(1),
						  invited smallint(1),
						  perm_modify smallint(1),
						  perm_delete smallint(1),
						  accept_code char(16),
						  primary key(resid, user_id)
						  )', 'Creating reservation_users table'),
					// Create reservation_user indexes
					array ('create index resusers_resid on reservation_users (resid)', 'Creating index'),
					array ('create index resusers_user_id on reservation_users (user_id)', 'Creating index'),
					array ('create index resusers_owner on reservation_users (owner)', 'Creating index'),
					// Create database user/permission
					array ("grant select, insert, update, delete
							on {$conf['db']['dbName']}.*
							to {$conf['db']['dbUser']}@{$conf['db']['hostSpec']} identified by '{$conf['db']['dbPass']}'", 'Creating database user')
					//, array ("SET PASSWORD FOR {$conf['db']['dbUser']}@{$conf['db']['hostSpec']} = OLD_PASSWORD('{$conf['db']['dbPass']}')", 'Fix MySQL 4.1+ password issue')
				*/);
	
	if ($conf['db']['drop_old'])	// Drop any old database with same name
		array_unshift($sqls, array ("drop database if exists {$conf['db']['dbName']}", 'Dropping database'));

	foreach ($sqls as $sql) {
		echo $sql[1] . '...';
		$result = $db->query($sql[0]);
		check_result($result);
	}
	
	// Create default lab
	$dbe = new DBEngine();
	echo 'Creating default lab...';
	$lab_id = $dbe->get_new_id();
	$result = $dbe->db->query('INSERT INTO labs VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)', array($lab_id,'default',480,1200,30,12,0,7,0,0,1,$conf['app']['adminEmail'],1,0));
	check_result($result);
}

/**
* Examine result and print success or failure message to browswer
* @param PEAR::DB $result pear::db result object
*/
function check_result($result) {
	global $failed;
	if (DB::isError($result)) {
		echo '<span style=\"color: #FF0000; font-weight: bold;\">Failed: </span>' . $result->getMessage() . "</span><br/><br/>\n";
		$failed = true;	
	}
	else
		echo "<span style=\"color: #00CD00;\">Success</span><br/><br/>\n";
}

function doFinish() {
	global $failed;
	echo '<h5>';
	if ($failed) {
		echo translate('There were errors during the install.');
	}
	else {
		echo translate('You have successfully finished setting up phpScheduleIt and are ready to begin using it.');
	}
	?>
	<br /><br />
	<?php
	echo translate('Thank you for using phpScheduleIt');
	echo '</h5>';
}
?>