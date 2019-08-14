<?php
/**
* Statistics page
* Print out visual statistics of many different aspects
*  of the application
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 07-18-05
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
* Include Stats class
*/
include_once('lib/Stats.class.php');

$stats = new Stats();

$t = new Template(translate('phpScheduleIt Statistics'));
$t->printHTMLHeader();

// Make sure this is the admin
if (!Auth::isAdmin()) {
    CmnFns::do_error_box(translate('This is only accessable to the administrator') . '<br />'
        . '<a href="ctrlpnl.php">' . translate('Back to My Control Panel') . '</a>');
}

$t->printWelcome();
$t->startMain();

$lab_id = isset($_GET['lab_id']) ? $_GET['lab_id'] : null;

if (!$stats->set_lab($lab_id)) {
	$stats->print_lab_error();
}
else {
	$stats->load_lab();
	
	print_lab_list($stats->get_lab_list(), $stats->lab_id);
	
	$stats->init();

	ob_start();		// There is a lot of HTML being printed, so buffer the output

	print_quick_stats($stats);
	
	print_system_stats($stats);
	
	$stats->set_stats(MONTH);
	$stats->print_stats();
	
	$stats->set_stats(DAY_OF_WEEK);
	$stats->print_stats();
	
	$stats->set_stats(DAY_OF_MONTH);
	$stats->print_stats();
	
	$stats->set_stats(START_TIME);
	$stats->print_stats();
	
	$stats->set_stats(END_TIME);
	$stats->print_stats();
	
	$stats->set_stats(RESOURCE);
	$stats->print_stats();
	
	$stats->set_stats(USER);
	$stats->print_stats();
	
	ob_end_flush();	// Print the buffered HTML to the browser
}
$t->endMain();

list($e_sec, $e_msec) = explode(' ', microtime());		// End execution timer
$tot = ((float)$e_sec + (float)$e_msec) - ((float)$s_sec + (float)$s_msec);
echo sprintf('<h6>Stats created in %.16f seconds</h6>', $tot);

$t->printHTMLFooter();	// Print HTML footer

?>