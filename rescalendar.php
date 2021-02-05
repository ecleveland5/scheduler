<?php
/**
* All specific views of an individual resource calendar will be available from this file
*  such as day/week/month view
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 02-18-05
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
include_once('lib/ResCalendar.class.php');
include_once('templates/cpanel.template.php');

// Check that the user is logged in
if (!Auth::is_logged_in()) {
    Auth::print_login_msg();
}
$print_view = filter_input(INPUT_GET, 'print_view', FILTER_SANITIZE_STRING);
$type = intval(filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING));
if ($type === null) {
	$type = MYCALENDARTYPE_DAY;
}
$machid = filter_input(INPUT_GET, 'machid', FILTER_SANITIZE_STRING);
$lab_id = filter_input(INPUT_GET, 'lab_id', FILTER_SANITIZE_STRING);

$t = new Template(translate('Resource Calendar'));
$calendar = new ResCalendar(Auth::getCurrentID(), $type, get_calendar_actual_date(), $machid, $lab_id);
	
	// Print HTML headers
	$t->printHTMLHeader();

if ($print_view!==null && $type===MYCALENDARTYPE_DAY) {
	$calendar->print_calendar(false, $print_view);
} else {
	
	
	// Print welcome box
	$t->printWelcome();
	
	// Begin main table
	$t->startMain();
	
	startQuickLinksCol();
	showQuickLinks();        // Print out My Quick Links
	startDataDisplayCol();
	
	$calendar->print_calendar();
	
	// End main table
	$t->endMain();
	
	list($e_sec, $e_msec) = explode(' ', microtime());        // End execution timer
	$tot = ((float)$e_sec + (float)$e_msec) - ((float)$s_sec + (float)$s_msec);
	echo '<!--Lab printout time: ' . sprintf('%.16f', $tot) . ' seconds-->';
	// Print HTML footer
	$t->printHTMLFooter();
}

/**
* Sets the 'actualDate' field of the MyCalendar object
* @param none
* @return datestamp of the viewed date
*/
function get_calendar_actual_date() {
	if (isset($_GET['date'])) {
		$date_split = explode('-', $_GET['date']);
	}
	else {
		$date_split = explode('-', date('m-d-Y'));
	}
	
	return mktime(0,0,0, $date_split[0], $date_split[1], $date_split[2]);	
}
?>