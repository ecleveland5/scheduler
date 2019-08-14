<?php
/**
* Allow searching and selection of a user
* Perform user specified function when selected
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 02-05-05
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Template class
*/
include_once('lib/Template.class.php');
/**
* SelectUser class
*/
include_once('lib/SelectUser.class.php');

// Check that the user is logged in
if (!Auth::is_logged_in()) {
    Auth::print_login_msg();
}

$first_name = $last_name = null;

if (isset($_GET['searchUsers'])) {					// Search for users or get all users?
	$first_name = trim($_GET['firstName']);
	$last_name = trim($_GET['lastName']);
}

$selectUserControl = new SelectUser($first_name, $last_name);
$selectUserControl->javascript = 'selectUserForReservation';

$t = new Template(translate('Select User'));

$t->printHTMLHeader();
// Begin main table
$t->startMain();

$selectUserControl->printUserTable();

// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();
?>
