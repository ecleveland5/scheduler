<?php
/**
* All specific views of the calendar will be available from this file
*  such as day/week/month view
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 01-22-05
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
include_once('lib/MyCalendar.class.php');
include_once('templates/cpanel.template.php');


// Check that the user is logged in
if (!Auth::is_logged_in()) {
    Auth::print_login_msg();
}

$t = new Template(translate('My Calendar'));

// Print HTML headers
$t->printHTMLHeader();

// Print welcome box
$t->printWelcome();

// Begin main table
$t->startMain();

startQuickLinksCol();
showQuickLinks();		// Print out My Quick Links
startDataDisplayCol();

$type = isset($_GET['view']) ? $_GET['view'] : MYCALENDARTYPE_DAY;

$calendar = new MyCalendar(Auth::getCurrentID(), $type, get_calendar_actual_date());

$calendar->print_calendar();

// End main table
$t->endMain();

list($e_sec, $e_msec) = explode(' ', microtime());		// End execution timer
$tot = ((float)$e_sec + (float)$e_msec) - ((float)$s_sec + (float)$s_msec);
echo '<!--Lab printout time: ' . sprintf('%.16f', $tot) . ' seconds-->';
// Print HTML footer
$t->printHTMLFooter();


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