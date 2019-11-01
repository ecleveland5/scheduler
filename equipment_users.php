<?php
/**
* Interface form for placing/modifying/viewing a reservation
* This file will present a form for a user to
*  make a new reservation or modify/delete an old one.
* It will also allow other users to view this reservation.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @version 02-21-05
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Template class
*/
include_once('lib/Template.class.php');
include_once('lib/Admin.class.php');
include_once('templates/cpanel.template.php');

$admin = new Admin('equipment_users');
$t = new Template();

// Make sure user is logged in
if (!Auth::is_logged_in()) {
	Auth::print_login_msg();
}

if (!Auth::isAdmin()) {
    CmnFns::do_error_box(translate('This is only accessible to the administrator') . '<br />'
        . '<a href="ctrlpnl.php">' . translate('Back to My Control Panel') . '</a>');
}else if (Auth::isAdmin()){
	if (strstr($_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF']) && isset($_POST['fn'])) {
		$t->set_title("Processing User Update");
		$t->printHTMLHeader();
		$t->startMain();
		
		$machid = $_POST['machid'];
		$users = $_POST['equipment_user_list'];

		$admin->db->clear_equipment_users($machid);
		$admin->db->add_equipment_users($machid, $users);

	}else{
		$t->set_title("Equipment Users");
		$t->printHTMLHeader();
		$t->startMain();
		
	}

	$admin->execute();
}


/*
else{
	CmnFns::do_error_box($admin->get_error_msg());
}
*/

// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();

?>