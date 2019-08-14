<?php
/**
* This file prints out a registration or edit profile form
* It will fill in fields if they are available (editing)
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 08-02-04
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Include Template class
*/

include_once('lib/Template.class.php');
include_once('templates/cpanel.template.php');

// Auth included in Template.php
$auth = new Auth();
$t = new Template();

$edit = isset($_GET['edit']);
$msg = '';
$show_form = true;
$signin = null;
if (isset($_POST['signin'])){
	$signin = $_POST['signin'];
}else if (isset($_GET['signin'])){
	$signin = $_GET['signin'];
}

// Check login status
if ($edit && !$auth->is_logged_in()) {
	$auth->print_login_msg(true);
	$auth->clean();			// Clean out any lingering sessions
}

// If we are editing and have not yet submitted an update
if ($edit && !isset($_POST['update'])) {
	$user = new User($_SESSION['sessionID']);
	$data = $user->get_user_data();
	$data['emailaddress'] = $data['email'];		// Needed to be the same as the form
}
else
	$data = CmnFns::cleanPostVals();

if (isset($_POST['register'])) {	// New registration
	$msg = $auth->do_register_user($data, $_SESSION['resume']);
	$show_form = false;
}
else if (isset($_POST['update'])) {	// Update registration
	//var_dump($data);
	$msg = $auth->do_edit_user($data);
	$show_form = false;
}

// Print HTML headers
$t->printHTMLHeader();

$t->set_title(($edit) ? translate('Modify My Profile') : translate('Register'));

// Print the welcome banner if they are logged in
if ($edit)
	$t->printWelcome();

// Begin main table
$t->startMain();

if($auth->is_logged_in()) {
	startQuickLinksCol();
	showQuickLinks();		// Print out My Quick Links
	startDataDisplayCol();
}

// Either this is a fresh view or there was an error, so show the form
if ($show_form || $msg != '') {
	//$auth->print_register_form($edit, $data, $msg, $signin);
	$auth->printRegisterForm($edit, $_POST, $msg, $auth);
}

// The registration/edit went fine, print the message
if ($msg == '' && $show_form == false) {
	$auth->print_success_box();
}

// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();
?>