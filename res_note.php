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
$resid = $_REQUEST['resid'];
$action = $_REQUEST['action'];
$type	= $_REQUEST['type'];
$billing_note	= $_REQUEST['billing_note'];
$technical_note = $_REQUEST['technical_note'];

$res = new Reservation($resid);

if (isset($_REQUEST['submit']) && strstr($_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF'])) {
	$t->set_title(translate("Processing Note"));
	$t->printHTMLHeader();
	$t->startMain();
	$res->add_billing_note($resid, $billing_note);
	$res->add_technical_note($resid, $technical_note);
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

	if($type=='billing'){
		$note = $res->get_billing_note();
	}else if ($type=='technical'){
		$note = $res->get_technical_note();
	}

	$t->set_title(ucwords($action + "ing Reservation Notes"));
    $t->printHTMLHeader();
    $t->startMain();
    print_res_note_box($resid, $action, $note);
}

// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();

