<?php
/**
* This file is the control panel, or "home page" for logged in users.
* It provides a listing of all upcoming reservations
*  and functionality to modify or delete them. It also
*  provides links to all other parts of the system.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @version 02-26-05
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2005 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Include Template class
*/
include_once('lib/Template.class.php');
/**
* Include control panel-specific output functions
*/
include_once('templates/cpanel.template.php');
include_once('lib/ResCalendar.class.php');
include_once('lib/Account.class.php');
include_once('lib/Reservation.class.php');

if (!Auth::is_logged_in()) {
    Auth::print_login_msg();	// Check if user is logged in
    echo "sessionID: " . $_SESSION['sessionID'];
}

$t = new Template(translate('My Control Panel'));
$db = new DBEngine();
$auth = new Auth();

$t->printHTMLHeader();
$t->printWelcome();
$t->startMain();

// Break table into 2 columns, put quick links on left side and all other tables on the right
startQuickLinksCol();
showQuickLinks();		// Print out My Quick Links
startDataDisplayCol();

$order = array('number');
$announcements = $db->get_announcements(time());

showAnnouncementTable( $announcements, $db->get_err() );

printCpanelBr();

$order = array('start_date', 'startTime', 'endTime', 'created', 'modified');

$res = $db->get_user_reservations($_SESSION['sessionID'], CmnFns::get_value_order($order), CmnFns::get_vert_order());

showReservationTable($res, $db->get_err());	// Print out My Reservations

printCpanelBr();

if ($auth->isAdmin()){

	//$Accounts = $db->get_table_data('accounts');
	//showAccountsTable($accounts, $db->get_err());
	//printCpanelBr();

	if (isset($_GET['signout']) && $_GET['signout']=='1'){
		$user_id = $_GET['user_id'];
		$where_clause = 'WHERE user_id = ?';
		$lab_id = $_GET['lab_id'];
		$signaction = 'signout';
		$signid = $_GET['signid'];
		
		$password = $db->get_table_data('user', array('password'), array('password'), NULL, NULL, $where_clause, array($user_id));
		$sign_msg = $auth->doSignin($user_id, $password[0]['password'], $lab_id, $signaction, $signid);
		if (!empty($sign_msg)) CmnFns::do_error_box($sign_msg, '', false);
	}

	//showSignedInUsers($auth->get_signedin_user_list());
	//printCpanelBr();

	include_once('lib/Admin.class.php');
	$admin = new Admin('today');
	$admin->execute();
	printCpanelBr();

	/*
	function get_calendar_actual_date() {
		if (isset($_GET['date'])) {
			$date_split = explode('-', $_GET['date']);
		}
		else {
			$date_split = explode('-', date('m-d-Y'));
		}
		
		return mktime(0,0,0, $date_split[0], $date_split[1], $date_split[2]);
	}
	
	$calendar = new ResCalendar(Auth::getCurrentID(), 1, get_calendar_actual_date(), $machid, $lab_id);

	showLabReservationsTable($calendar);
	*/
	
/*
	$pager = CmnFns::getNewPager();
	$num = $db->get_num_pending_res();	// Get number of records
	$pager->setTotRecords($num);		// Pager method calls
	
	$orders = array('start_date', 'end_date', 'name', 'last_name', 'startTime', 'endTime');
	$res = $db->get_reservation_data($pager, $orders, true);
	print_approve_reservations($pager, $res, $db->get_err());
*/


}else{
	if ($conf['app']['use_perms']) {
		
		$order = array('name', 'nickname');
		showTrainingTable($db->get_user_permissions($_SESSION['sessionID']), $db->get_err());	// Print out My Training
		printCpanelBr();
	}

}

//showInvitesTable($db->get_user_invitations(Auth::getCurrentID(), true), $db->get_err());
//printCpanelBr();

//showParticipatingTable($db->get_user_invitations(Auth::getCurrentID(), false), $db->get_err());

endDataDisplayCol();
$t->endMain();
printCpanelBr();
$t->printHTMLFooter();
?>