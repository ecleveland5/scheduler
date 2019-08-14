<?php
/**
* Scheduler Application
* This file contians the scheduler application where
* users have an interface for reserving resources,
* viewing other reservations and modifying their own.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 07-18-04
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
list($s_sec, $s_msec) = explode(' ', microtime());	// Start execution timer
/**
* Include Template class
*/
include_once('lib/Template.class.php');
/**
* Include scheduler-specific output functions
*/
include_once('lib/Lab.class.php');
include_once('templates/cpanel.template.php');

// Check that the user is logged in
if (!Auth::is_logged_in()) {
    Auth::print_login_msg();
}
$user = new User(Auth::getCurrentID());
$t = new Template('Make a Reservation');
if (!isset($_GET['lab_id'])) {
	$_GET['lab_id'] = $user->get_lab_pref();
}
$s = new Lab($_GET['lab_id']);

// Print HTML headers
$t->printHTMLHeader();

// Print welcome box
$t->printWelcome();

// Begin main table
$t->startMain();

startQuickLinksCol();
showQuickLinks();		// Print out My Quick Links
startDataDisplayCol();

ob_start();		// The lab may take a long time to print out, so buffer all of that HTML data

if ($s->isValid) {
	$s->print_jump_links();
    $filters = array(); //$user->get_resource_filters($s->lab_id);
	$s->print_lab($filters);

	// Print out links to jump to new date
	$s->print_jump_links();
}
else {
	$s->print_error();
}

ob_end_flush();	// Write all of the HTML out

// End main table
$t->endMain();

list($e_sec, $e_msec) = explode(' ', microtime());		// End execution timer
$tot = ((float)$e_sec + (float)$e_msec) - ((float)$s_sec + (float)$s_msec);
echo '<!--Lab printout time: ' . sprintf('%.16f', $tot) . ' seconds-->';
// Print HTML footer
$t->printHTMLFooter();
?>