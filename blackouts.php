<?php
/**
* Blackout Scheduler Application
* Manage blackout times from this file
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 06-24-04
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
include_once('templates/cpanel.template.php');
/**
* Include scheduler-specific output functions
*/
include_once('lib/Lab.class.php');

$user = new User(Auth::getCurrentID());
$t = new Template(translate('Manage Blackout Times'));
$lab_id = filter_input(INPUT_GET, 'lab_id');
if (is_null($lab_id)) {
    $lab_id = $user->get_lab_pref();
}
$s = new Lab($lab_id, BLACKOUT_ONLY);

// Print HTML headers
$t->printHTMLHeader();

// Check that the admin is logged in
if (!Auth::isAdmin()) {
    CmnFns::do_error_box(translate('This is only accessable to the administrator') . '<br />'
        . '<a href="ctrlpnl.php">' . translate('Back to My Control Panel') . '</a>');
}

// Print welcome box
$t->printWelcome();

// Begin main table
$t->startMain();
startQuickLinksCol();
showQuickLinks();		// Print out My Quick Links
startDataDisplayCol();
$filter = array();
$s->print_jump_links();
$s->print_lab($filter);

// Print out links to jump to new date
$s->print_jump_links();

// End main table
$t->endMain();

list($e_sec, $e_msec) = explode(' ', microtime());		// End execution timer
$tot = ((float)$e_sec + (float)$e_msec) - ((float)$s_sec + (float)$s_msec);
echo '<!--Lab printout time: ' . sprintf('%.16f', $tot) . ' seconds-->';
// Print HTML footer
$t->printHTMLFooter();
?>
