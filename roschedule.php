<?php
/**
* Read only view of the lab
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 06-25-04
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

$t = new Template(translate('Online Scheduler [Read-only Mode]'));
$s = new Lab((isset($_GET['lab_id']) ? $_GET['lab_id'] : null), READ_ONLY);

// Print HTML headers
$t->printHTMLHeader();

CmnFns::do_message_box('Click on the shaded, reserved time boxes to see the details of the reservation.');

// Begin main table
$t->startMain();

$s->printJumpLinks();
echo "<br>";
$filters = array();
$s->printLab($filters);

// Print out links to jump to new date
//$s->print_jump_links();

CmnFns::do_message_box('<a href="index.php">' . translate('Login to view details and place reservations') . '</a>');

// End main table
$t->endMain();

list($e_sec, $e_msec) = explode(' ', microtime());		// End execution timer
$tot = ((float)$e_sec + (float)$e_msec) - ((float)$s_sec + (float)$s_msec);
echo '<!--Lab printout time: ' . sprintf('%.16f', $tot) . ' seconds-->';
// Print HTML footer
$t->printHTMLFooter();
?>