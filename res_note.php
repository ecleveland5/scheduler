<?php
/**
* Interface form for placing/modifying/viewing a reservation note
* This file will present a form for a user to
* make a new reservation note or modify/delete an old one.
* @author Ernie Cleveland <eclevela@umd.edu>
* @version 08-30-07
* @package phpScheduleIt
*
* License: GPL, see LICENSE
*/
/**
* Template class
*/
include_once('lib/Template.class.php');
include_once('lib/Reservation.class.php');

if (!Auth::is_logged_in()) {
	Auth::print_login_msg();
}

$t = new Template();
$res_id = filter_input(INPUT_GET, 'resid', FILTER_SANITIZE_STRING);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$type	= filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
$billing_note	= filter_input(INPUT_GET, 'billing_note', FILTER_SANITIZE_STRING);
$technical_note = filter_input(INPUT_GET, 'technical_note', FILTER_SANITIZE_STRING);
$submit = filter_input(INPUT_GET, 'submit', FILTER_SANITIZE_STRING);

$res = new Reservation($res_id);

if ($submit && strstr($_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF'])) {
	$t->set_title(translate("Processing Note"));
	$t->printHTMLHeader();
	$t->startMain();
	$res->add_billing_note($res_id, $billing_note);
	$res->add_technical_note($res_id, $technical_note);
	?>
	<center>
	<div class="message">
	The notes were updated.
	<br><br>
	<?
	echo '<script language="JavaScript" type="text/javascript">' . "\n"
		. 'window.opener.document.location.href = window.opener.document.URL;' . "\n"
		. '</script>'
		. '<br/><a href="javascript: window.close();">' . translate('Close') . '</a>';
	?>
	</div>
	</center>
	<?
}
else {

	if ($type=='billing'){
		$note = $res->get_billing_note();
	} else if ($type === 'technical'){
		$note = $res->get_technical_note();
	}

	$t->set_title(ucwords($action + "ing Reservation Notes"));
    $t->printHTMLHeader();
    $t->startMain();
    print_res_note_box($res_id, $action, $note);
}

// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();

