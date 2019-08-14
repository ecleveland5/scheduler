<?php
/**
* Interface form for placing/modifying/viewing a reservation
* This file will present a form for a user to
*  make a new reservation or modify/delete an old one.
* It will also allow other users to view this reservation.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @author Ernie Cleveland <eclevela@umd.edu>
* @version 04-16-09
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2009 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Template class
*/
include_once('lib/Template.class.php');
include_once('lib/User.class.php');
include_once('lib/Auth.class.php');
include_once('lib/Reservation.class.php');
include_once('templates/cpanel.template.php');


if (!Auth::is_logged_in()) {
	Auth::print_login_msg();
}

// check permissions to add/delete/modify account data
$db = new DBEngine();
$t = new Template();
$auth = new Auth();
$pager = CmnFns::getNewPager();
$pager->setTextStyle('font-size: 10px;');

$t->set_title("My Reservations : Maryland NanoCenter Scheduler");
$t->printHTMLHeader();
$t->printWelcome();
$t->startMain();


startQuickLinksCol();
showQuickLinks();		// Print out My Quick Links
startDataDisplayCol();

$user = new User($auth->getCurrentID());

$num = $user->get_num_reservations();	// Get number of records	
$pager->setTotRecords($num);				// Pager method calls
$orders = array('start_date', 'name', 'last_name', 'startTime', 'endTime');

$res = $user->get_my_reservation_data($pager, $orders);
showReservationTable($res, $db->get_err(), $db->get_resources_in_use());	// Print out My Reservations

$pager->printPages();


// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();
?>