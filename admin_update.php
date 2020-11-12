<?php
/**
 * Provides interface for making all administrative database changes
 * @author Nick Korbel <lqqkout13@users.sourceforge.net>
 * @author David Poole <David.Poole@fccc.edu>
 * @version 02-27-05
 * @package Admin
 *
 * Copyright (C) 2003 - 2005 phpScheduleIt
 * License: GPL, see LICENSE
 */
/**
 * Template class
 */
include_once('lib/Template.class.php');
/**
 * Include Admin class
 */
include_once('lib/Admin.class.php');
/**
 * Include PHPMailer
 */
include_once('lib/PHPMailer.class.php');
/**
 * Include User class
 */
include_once('lib/User.class.php');

// Make sure this is the admin and is being called from admin.php

 if ( (!Auth::isAdmin()) || (!strstr($_SERVER['HTTP_REFERER'],'admin.php')) ) {
 CmnFns::do_error_box(translate('This is only accessable to the administrator') . '<br />'
 . '<a href="ctrlpnl.php">' . translate('Back to My Control Panel') . '</a>');
 die;
 }


$db = new AdminDB();

$tools = array (
	'deleteUsers' => 'del_users',
	'editLabUsers' => 'edit_lab_users',

	'addResource'	=> 'add_resource',
	'editResource'	=> 'edit_resource',
	'delResource'	=> 'del_resource',
	'togResource'	=> 'tog_resource',
	'updateResources' => 'update_resources',
	'editEquipmentUsers'	=> 'edit_equipment_users',

	'addAccount'	=> 'add_account',
	'editAccount'	=> 'edit_account',
	'delAccount'	=> 'del_account',
	'togAccount'	=> 'tog_account',
	'editAccountUsers' =>	'edit_account_users',
	'editUserAccounts' => 'edit_user_accounts',

	'editPerms' =>	'edit_perms',

	'resetPass' => 'reset_password',

	'addLab'	=> 'add_lab',
	'editLab'	=> 'edit_lab',
	'delLab'	=> 'del_lab',
	'dfltLab'	=> 'set_default_lab',

	'addAnnouncement'	=> 'add_announcement',
	'editAnnouncement'	=> 'edit_announcement',
	'delAnnouncement'	=> 'del_announcement',

	'adminToggle' => 'toggle_admin'
);

//echo "post[fn]: " . $_POST['fn'] . "<br>";
//echo "accounts: ";
//if(isset($_POST['account_id'])) print_r($_POST['account_id']);
//echo "<br>";

$fn = isset($_POST['fn']) ? $_POST['fn'] : (isset($_GET['fn']) ? $_GET['fn'] : '');	// Set function

if (!isset($tools[$fn]) && !isset($tools[$fn])) {		// Validate tool
	CmnFns::do_error_box(translate('Could not determine tool')
		. '<br/><a href="ctrlpnl.php">' . translate('Back to My Control Panel') . '</a>');
	die;
}
else {
	if (isset($tools[$fn]))
		eval($tools[$fn] . '();');
}

unset($fn, $tools);

/**
 * Adds a lab to the database
 * @param none
 */
function add_lab() {
	global $db;
	global $conf;

	$lab = check_lab_data(CmnFns::cleanPostVals());
	$id = $db->add_lab($lab);

	CmnFns::write_log('Lab added. ' . $lab['labTitle'], $_SESSION['sessionID']);
	print_success();
}

/**
 * Edits lab data
 * @param none
 */
function edit_lab() {
	global $db;

	$lab = check_lab_data(CmnFns::cleanPostVals());
	$db->edit_lab($lab);

	CmnFns::write_log('Lab editied. ' . $lab['labTitle'] . ' ' . $lab['lab_id'], $_SESSION['sessionID']);
	print_success();
}

/**
 * Deletes a list of resources
 * @param none
 */
function del_lab() {
	global $db;

	$lab_id = $_POST['lab_id'];

	// Make sure machids are checked
	if (empty($lab_id))
		print_fail(translate('You did not select any labs to delete.'));

	$db->del_lab($lab_id);
	CmnFns::write_log('Labs deleted. ' . join(', ', $lab_id), $_SESSION['sessionID']);
	print_success();
}

function set_default_lab() {
	global $db;

	$db->set_default_lab($_POST['lab_id']);
	CmnFns::write_log('Default lab changed to ' . $_POST['lab_id'], $_SESSION['sessionID']);
	print_success();
}

/**
 * Adds an announcement to the database
 * @param none
 */
function add_announcement() {
	global $db;
	global $conf;

	$announcement = check_announcement_data(CmnFns::cleanPostVals());
	$id = $db->add_announcement($announcement);

	CmnFns::write_log('Announcement added. ' . $announcement['announcement'], $_SESSION['sessionID']);
	print_success();
}

/**
 * Edits announcement data
 * @param none
 */
function edit_announcement() {
	global $db;

	$announcement = check_announcement_data(CmnFns::cleanPostVals());
	$db->edit_announcement($announcement);

	CmnFns::write_log('Announcement editied. ' . $announcement['announcement'] . ' ' . $announcement['announcementid'], $_SESSION['sessionID']);
	print_success();
}

/**
 * Deletes a list of announcements
 * @param none
 */
function del_announcement() {
	global $db;

	$announcementid = $_POST['announcementid'];

	// Make sure machids are checked
	if (empty($announcementid))
		print_fail('You did not select any announcements to delete.');

	$db->del_announcement($announcementid);
	CmnFns::write_log('Announcements deleted. ' . join(', ', $announcementid), $_SESSION['sessionID']);
	print_success();
}

/**
 * Deletes a list of users from the database
 * @param none
 */
function del_users() {
	global $db;
	$user_ids = filter_input(INPUT_POST, 'user_id', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
	$initial_archived_users = explode(",", filter_input(INPUT_POST, 'initial_archived_users', FILTER_DEFAULT));
	$undelete_users = array_diff($initial_archived_users, $user_ids);
	
	// Make sure user_ids are checked
	if (empty($user_ids)) {
		print_fail(translate('You did not select any members to delete.') . '<br />');
	}
	
	if (!empty($undelete_users)) {
		$db->undelete_users($undelete_users);
	}
	
	$db->del_users($user_ids);
	CmnFns::write_log('Users deleted. ' . join(', ', $user_ids), $_SESSION['sessionID']);
	print_success();
}

/**
 * Deletes a list of users from the database
 * @param none
 */
function edit_lab_users() {
	global $db;

	$lab_id = $_REQUEST['lab_id'];
	$viewed_users = explode(",",$_REQUEST['viewed_users']);

	$trained = $_REQUEST['trained'];
	$lab_admin = $_REQUEST['lab_admin'];

	/*
    echo "<br><br>";
    echo "viewed:";
    var_dump($viewed_users);
    echo "<br><br>";
    echo "trained:";
    var_dump($trained);
    echo "<br><br>";
    echo "admins:";
    var_dump($lab_admin);
    */

	$db->clear_lab_permissions($lab_id, $viewed_users);
	$db->add_lab_training($lab_id, $trained);
	$db->add_lab_admins($lab_id, $lab_admin);


	//$db->edit_lab_users($_POST['user_id'], $_POST['lab_id']);
	CmnFns::write_log('Users deleted. ' . join(', ', $_POST['user_id']), $_SESSION['sessionID']);
	print_success();
}

/**
 * Adds a resource to the database
 * @param none
 */
function add_resource() {
	global $db;
	global $conf;

	$resource = check_equipment_data(CmnFns::cleanPostVals());
	$id = $db->add_resource($resource);

	if (isset($resource['autoAssign']))		// Automatically give all users permission to reserve this resource
		$db->auto_assign($id);

	CmnFns::write_log('Resource added. ' . $resource['name'], $_SESSION['sessionID']);
	print_success();
}

/**
 * Edits resource data
 * @param none
 */
function edit_resource() {
	global $db;

	$resource = check_equipment_data(CmnFns::cleanPostVals());
	//var_dump($resource);
	$db->edit_resource($resource);

	if (isset($resource['autoAssign']))		// Automatically give all users permission to reserve this resource
		$db->auto_assign($resource['machid']);

	CmnFns::write_log('Resource editied. ' . $resource['name'] . ' ' . $resource['machid'], $_SESSION['sessionID']);
	print_success();
}

/**
 * Deletes a list of resources
 * @param none
 */
function del_resource() {
	global $db;
	
	$resource_list_shown = filter_input(INPUT_POST, 'resource_list_shown', FILTER_SANITIZE_STRING);
	$machid = filter_input(INPUT_POST, 'machid', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

	$resource_list_shown = explode(',', $resource_list_shown);
	$db->del_resource($machid, $resource_list_shown);
	
	CmnFns::write_log('Resources deleted. ' . join(', ', $machid), $_SESSION['sessionID']);
	print_success();
}

function update_resources() {
	global $db;
	
	$values = array();
	
	foreach ($_POST as $key=>$value) {
		if (strstr($key, 'operational_status') !== false) {
			$op_status_id = explode('-', $key);
			$values[filter_var($op_status_id[1], FILTER_SANITIZE_STRING)] = filter_var($value, FILTER_SANITIZE_STRING);
		}
	}
	
	$db->update_resource_op_status($values);
	
	
	$resource_list_shown = explode(',', filter_input(INPUT_POST, 'resource_list_shown', FILTER_SANITIZE_STRING));
	$machid = filter_input(INPUT_POST, 'machid', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
	
	// Make sure account_ids are checked
	$account_list_shown = explode(',', $resource_list_shown);
	$db->del_resource($machid, $resource_list_shown);
	
	print_success();
}

/**
 * Toggles a resource active/inactive
 * @param none
 */
function tog_resource() {
	global $db;

	$db->tog_resource($_GET['machid'], $_GET['status']);
	CmnFns::write_log('Resource ' . $_GET['machid'] . ' toggled on/off.', $_SESSION['sessionID']);
	print_success();
}

/**
 * Adds an account to the database
 * @param none
 */
function add_account() {
	global $conf;

	$account_data = check_account_data(CmnFns::cleanPostVals());
	$account = new Account(NULL, false);

	$account->add_account($account_data);

	$account->add_account_user($account->get_field("pi_id"), 1);

	//CmnFns::write_log('Account added. ' . $account['name'], $_SESSION['sessionID']);
	print_success();
}

/**
 * Edits account data
 * @param none
 */
function edit_account() {
	global $db;
	$account = check_account_data(CmnFns::cleanPostVals());

	//if($db->is_account_admin($_SESSION['sessionID'], $account['account_id'])){

	$db->edit_account($account);

	CmnFns::write_log('Account editied. ' . $account['name'] . ' ' . $account['account_id'], $_SESSION['sessionID']);
	print_success();
	//}else{
	//print_fail();
	//}
}

/**
 * Deletes a list of accounts
 * @param none
 */
function del_account() {
	global $db;
	$account_list_shown = filter_input(INPUT_POST, 'account_list_shown', FILTER_SANITIZE_STRING);
	$account_id = filter_input(INPUT_POST, 'account_id', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
	
	// Make sure account_ids are checked
	$account_list_shown = explode(',', $account_list_shown);
	$db->del_account($account_id, $account_list_shown);
	CmnFns::write_log('Accounts archived. ' . join(', ', $account_id), $_SESSION['sessionID']);
	print_success();
}

/**
 * Toggles an account active/inactive
 * @param none
 */
function tog_account() {
	global $db;
	$account_id = filter_input(INPUT_GET, 'account_id', FILTER_SANITIZE_STRING);
	$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);
	
	$db->tog_account($account_id, $status);
	CmnFns::write_log('Account ' . $account_id . ' toggled on/off.', $_SESSION['sessionID']);
	$acc = new Account($account_id);
	$acc->email_account_admins($status);
	print_success();
}

/**
 * Validates lab data
 * @param array $data array of data to validate
 */
function check_account_data($data) {
	$msg = array();

	if (empty($data['FRS']))
		array_push($msg, 'FRS # is required.');

	// if pi is empty check for pi_first_name and pi_last_name
	if (empty($data['pi'])) {
		//array_push($msg, 'A PI is required.');
		if(empty($data['pi_first_name']))
			array_push($msg, 'Please enter the PI\'s first name');
		if(empty($data['pi_last_name']))
			array_push($msg, 'Please enter the PI\'s last name');
	} else {
		if(!is_numeric($data['pi']))
			array_push($msg, 'Invalid PI.');
	}

	$data['status'] 			= 1;


	if (!empty($msg))
		print_fail($msg, $data);

	return $data;
}


/**
 *
 * @return none
 */
function edit_account_users() {
	global $db;
	$account_id = filter_input(INPUT_POST, 'account_id', FILTER_SANITIZE_STRING);

	$account = new Account($account_id);

	// Make sure user_ids are checked
	$users = $_POST['user_list'];
	$admins = $_POST['admin_list'];

	$keptUsers = array_merge($users, $admins);
	//var_dump($keptUsers);
	$account->db->clear_account_users($account->get_account_id(), $keptUsers);

	foreach($users as $acc_user) {
		$account->add_account_user($acc_user);
	}

	foreach($admins as $admin_user) {
		$account->add_account_user($admin_user, 1);
	}

	//$db->edit_account_users($_REQUEST['account_id'], $_REQUEST['user_id'], $_REQUEST['is_admin']);
	//CmnFns::write_log('Account users have been updated. ' . join(', ', $_POST['user_id']), $_SESSION['sessionID']);

	print_success();

}


/**
 *
 */
function edit_equipment_users() {
	global $db;
	$machid = filter_input(INPUT_GET, 'machid', FILTER_SANITIZE_STRING);

	$users = filter_input(INPUT_GET, 'equipment_user_list', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);

	$db->clear_equipment_users($machid);
	$db->add_equipment_users($machid, $users);

	print_success();
}

/**
 *
 */
function edit_user_accounts() {
	global $db;
	$user_id = $_POST['user_id'];
	//$user_array = array($user_id);
	$user = new User($user_id);

	$accounts = $_POST['account_list'];
	$admin_accounts = $_POST['admin_list'];
	$user->db->clear_user_account($user_id);

	foreach($accounts as $acct) {
		$account = new Account($acct);
		$account->add_account_user($user_id, 0);
	}

	foreach($admin_accounts as $acct) {
		$account = new Account($acct);
		$account->add_account_user($user_id, 1);
	}

	print_success();
}


/**
 * Validates lab data
 * @param array $data array of data to validate
 * @return validated data
 */
function check_lab_data($data) {
	$rs = array();
	$msg = array();

	if (empty($data['labTitle']))
		array_push($msg, translate('Lab title is required.'));
	else
		$rs['labTitle'] = $data['labTitle'];
	$rs['nickname'] = $data['nickname'];
	$rs['description'] = $data['description'];
	$rs['director'] = $data['director'];
	$rs['manager'] = $data['manager'];
	$rs['building'] = $data['building'];
	$rs['room_number'] = $data['room_number'];
	$rs['url'] = $data['url'];
	$rs['phone'] = $data['phone'];
	$rs['priority'] = $data['priority'];
	$rs['summary'] = $data['summary'];
	$rs['type'] = $data['type'];
	$rs['visibility'] = $data['visibility'];

	if (intval($data['dayStart']) >= intval($data['dayEnd']))
		array_push($msg, translate('Invalid start/end times'));
	else {
		$rs['dayStart']	= $data['dayStart'];
		$rs['dayEnd']	= $data['dayEnd'];
	}

	$rs['weekDayStart']	= $data['weekDayStart'];
	$rs['timeSpan'] = $data['timeSpan'];
	$rs['isHidden'] = $data['isHidden'];
	$rs['showSummary'] = $data['showSummary'];

	if (empty($data['viewDays']) || $data['viewDays'] <= 0)
		array_push($msg, translate('View days is required'));
	else
		$rs['viewDays'] = intval($data['viewDays']);

	if ($data['dayOffset'] == '' || $data['dayOffset'] < 0)
		array_push($msg, translate('Day offset is required'));
	else
		$rs['dayOffset'] = intval($data['dayOffset']);

	if (empty($data['adminEmail']))
		array_push($msg, translate('Admin email is required'));
	else
		$rs['adminEmail']	= $data['adminEmail'];

	if (isset($data['lab_id']))
		$rs['lab_id'] = $data['lab_id'];

	if (!empty($msg))
		print_fail($msg, $data);

	return $rs;
}

/**
 * Validates announcement data
 * @param array $data array of data to validate
 * @return validated data
 */
function check_announcement_data($data) {
	$rs = array();
	$msg = array();

	if (empty($data['announcement']))
		array_push($msg, translate('Announcement text is required.'));
	else
		$rs['announcement'] = $data['announcement'];

	if (empty($data['number']) || !is_numeric($data['number']) || $data['number'] < 0)
		array_push($msg, translate('Announcement number is required.'));
	else
		$rs['number'] = intval($data['number']);

	if (isset($data['announcementid']))
		$rs['announcementid'] = $data['announcementid'];

	$start_hour = $end_hour = $start_minute = $end_minute = 0;
	if (isset($data['use_start_time'])) {
		// Validate the starting hour
		if (!isset($data['start_hour']) || empty($data['start_hour']) || intval($data['start_hour']) < 0 || (intval($data['start_hour']) == 12 && $data['start_ampm'] == 'am')) {
			$start_hour = 0;
		}
		else if (intval($data['start_hour']) > 23) {
			$start_hour = 23;
		}
		else if (intval($data['start_hour']) < 12 && $data['start_ampm'] === 'pm') {
			$start_hour = intval($data['start_hour']) + 12;
		}
		else {
			$start_hour = intval($data['start_hour']);
		}
		// Validate the starting minute
		if (!isset($data['start_min']) || empty($data['start_min']) || intval($data['start_min']) < 0) {
			$start_minute = 0;
		}
		else if (intval($data['start_min']) > 59) {
			$start_minute = 59;
		}
		else {
			$start_minute = intval($data['start_min']);
		}
	}
	if (isset($data['use_end_time'])) {
		// Validate the ending hour
		if (!isset($data['end_hour']) || empty($data['end_hour']) || intval($data['end_hour']) < 0 || (intval($data['end_hour']) == 12 && $data['end_ampm'] == 'am')) {
			$end_hour = 0;
		}
		else if (intval($data['end_hour']) > 23) {
			$end_hour = 23;
		}
		else if (intval($data['end_hour']) < 12 && $data['end_ampm'] === 'pm') {
			$end_hour = intval($data['end_hour']) + 12;
		}
		else {
			$end_hour = intval($data['end_hour']);
		}
		// Validate the ending minute
		if (!isset($data['end_min']) || empty($data['end_min']) || intval($data['end_min']) < 0) {
			$end_minute = 0;
		}
		else if (intval($data['end_min']) > 59) {
			$end_minute = 59;
		}
		else {
			$end_minute = intval($data['end_min']);
		}
	}

	// Complete the starting/ending time values
	if (isset($data['use_start_time'])) {
		$start_date_vals = split(INTERNAL_DATE_SEPERATOR, $data['start_date']);
		$starting_time = mktime($start_hour, $start_minute, 0, $start_date_vals[0], $start_date_vals[1], $start_date_vals[2]);
	}
	else {
		$starting_time = null;
	}
	if (isset($data['use_end_time'])) {
		$end_date_vals = split(INTERNAL_DATE_SEPERATOR, $data['end_date']);
		$ending_time = mktime($end_hour, $end_minute, 0, $end_date_vals[0], $end_date_vals[1], $end_date_vals[2]);
	}
	else {
		$ending_time = null;
	}

	$rs['start_datetime'] = $starting_time;
	$rs['end_datetime'] = $ending_time;
	$rs['lab_id'] = $data['lab_id'];

	if (!empty($msg)) {
		print_fail($msg, $data);
	}

	return $rs;
}

/**
 * Validates resource data
 * @param array $data array of data to validate
 * @return validated data
 */
function check_equipment_data($data) {
	$rs = array();
	$msg = array();

	if (isset($data['allow_multi'])) {
		$minRes = 0;
		$maxRes = 1440;
	}
	else {
		$minRes = intval($data['minH'] * 60 + $data['minM']);
		$maxRes = intval($data['maxH'] * 60 + $data['maxM']);
	}
	$data['minRes']	= $minRes;
	$data['maxRes']	= $maxRes;

	if (empty($data['name']))
		array_push($msg, translate('Resource name is required.'));
	else
		$rs['name'] = $data['name'];

	if (empty($data['lab_id']))
		array_push($msg, translate('Valid lab must be selected'));
	else
		$rs['lab_id'] = $data['lab_id'];

	if (intval($minRes) > intval($maxRes)) {
		array_push($msg, translate('Minimum reservation length must be less than or equal to maximum reservation length.'));
	}
	else {
		$rs['minRes']	= $minRes;
		$rs['maxRes']	= $maxRes;
	}

	$rs['rphone']	= $data['rphone'];
	$rs['location'] = $data['location'];
	$rs['notes']	= $data['notes'];
	$rs['owner']	= $data['owner'];
	$rs['staff_contact']	= $data['staff_contact'];
	
	// adding dynamic rates for this tool
	$keys = array_keys($data);
	// find the inputs corresponding to resource_rates, the resource_rate_id is after the :
	$rate_categories = preg_grep('/resource_rate:(\d+)/', $keys);
	foreach ($rate_categories as $rate_category) {
		$rate_category_id = explode(':', $rate_category);
		// check if rates are numeric
		if (!is_numeric($data['resource_rate:'.$rate_category_id[1]])) {
			$msg = 'Please enter a numberic value for the equipment rates.';
		}
		$rs['resource_rates'][$rate_category_id[1]] = $data['resource_rate:'.$rate_category_id[1]];
	}

	$rs['edit_horizon'] = $data['edit_horizon'];

	if (isset($data['autoAssign']))
		$rs['autoAssign'] = $data['autoAssign'];

	if (isset($data['approval']))
		$rs['approval'] = $data['approval'];

	if (isset($data['allow_multi']))
		$rs['allow_multi'] = $data['allow_multi'];

	if (isset($data['machid']))
		$rs['machid'] = $data['machid'];

	if (!empty($msg))
		print_fail($msg, $data);

	return $rs;
}

/**
 * Edit user permissions for what resources they can reserve
 * @param none
 */
function edit_perms() {
	global $db;

	$db->clear_perms($_POST['user_id']);
	$db->set_perms($_POST['user_id'], isset($_POST['machid']) ? $_POST['machid'] : array());
	CmnFns::write_log('Permissions changed for user ' . $_POST['user_id'], $_SESSION['sessionID']);

	if (isset($_POST['notify_user']))
		send_perms_email($_POST['user_id']);

	print_success();
}

/**
 * Sends a notification email to the user that thier permissions have been updated
 * @param string $user_id id of member
 * @param array $machids array of resource ids that the user now has permission on
 */
function send_perms_email($user_id) {
	global $conf;

	$adminEmail = $conf['app']['adminEmail'];
	$appTitle = $conf['app']['title'];

	$user = new User($user_id);
	$perms = $user->get_perms();

	$subject = $appTitle . ' ' . translate('Permissions Updated');
	$msg = $user->get_first_name() . ",\r\n"
		. translate('Your permissions have been updated', array($appTitle)) . "\r\n\r\n";
	$msg .= (empty($perms)) ? translate('You now do not have permission to use any resources.') . "\r\n" : translate('You now have permission to use the following resources') . "\r\n";
	foreach ($perms as $val)
		$msg .= $val . "\r\n";	// Add each resource name

	$msg .= "\r\n" . translate('Please contact with any questions.', array($adminEmail));

	mail($user->get_email(), $subject, $msg, 'From: '.$adminEmail.'\r\n');
}

/**
 * Reset the password for a user
 * @param none
 */
function reset_password() {
	global $db;
	global $conf;

	$data = CmnFns::cleanPostVals();

	$password = empty( $data['password'] ) ? $conf['app']['defaultPassword'] : stripslashes($data['password']);
	$db->reset_password($data['user_id'], $password);

	if (isset($data['notify_user']))
		send_pwdreset_email($data['user_id'], $password);

	CmnFns::write_log('Password reset by admin for user ' . $_POST['user_id'], $_SESSION['sessionID']);
	print_success();
}

/**
 * Send a notification email that the password has been reset
 * @param string $user_id id of member
 * @param string $password new password for user
 */
function send_pwdreset_email($user_id, $password) {
	global $conf;

	$adminEmail = $conf['app']['adminEmail'];
	$appTitle = $conf['app']['title'];

	$user = new User($user_id);

	$subject = $appTitle . ' ' . translate('Password Reset');
	$msg = $user->get_first_name() . ",\r\n"
		. translate_email('password_reset', $appTitle, $password, $appTitle, CmnFns::getScriptURL(), $adminEmail);

	mail($user->get_email(), $subject, $msg, 'From: '.$adminEmail.'\r\n');
}

/**
 * Changes a users 'is_admin' status to give or take away admin privleges
 * @param none
 */
function toggle_admin() {
	global $db;

	$is_admin = 0;

	if (isset($_GET['status']) && $_GET['status'] == 1) { $is_admin = 1; }

	$db->change_admin_status($_GET['user_id'], $is_admin);

	CmnFns::write_log('Admin status chagned for user: ' . $_GET['user_id'], $_SESSION['sessionID']);
	print_success();
}

/**
 * Prints a page with a message notifying the admin of a successful update
 * @param none
 */
function print_success() {
	// Get the name/value of anything that was currently being edited
	// This will then be flitered out of the link back so that item will not show up in the edit box
	$return = (!empty($_POST['get'])) ? preg_replace('/&' . $_POST['get'] . '=[\d\w]*/', '', $_SERVER['HTTP_REFERER']) : $_SERVER['HTTP_REFERER'];
	header("Refresh: 2; URL=$return");		// Auto send back after 2 seconds
	$t = new Template(translate('Successful update'));
	$t->printHTMLHeader();
	$t->printWelcome();
	$t->startMain();
	CmnFns::do_message_box(translate('Your request was processed successfully.') . '<br />'
		. '<a href="' . $return. '">' . translate('Go back to system administration') . '</a><br />'
		. translate('Or wait to be automatically redirected there.'));
	$t->endMain();
	$t->printHTMLFooter();
	die;
}

/**
 * Prints a page notifiying the admin that the requirest failed.
 * It will also assign the data passed in to a session variable
 *  so it can be reinserted into the form that it came from
 * @param string or array $msg message(s) to print to user
 * @param array $data array of data to post back into the form
 */
function print_fail($msg, $data = null) {
	if (!is_array($msg))
		$msg = array ($msg);

	if (!empty($data))
		$_SESSION['post'] = $data;

	$t = new Template(translate('Update failed!'));
	$t->printHTMLHeader();
	$t->printWelcome();
	$t->startMain();
	CmnFns::do_error_box(translate('There were problems processing your request.') . '<br /><br />'
		. '- ' . join('<br />- ', $msg) . '<br />'
		. '<br /><a href="' . $_SERVER['HTTP_REFERER'] . '">' . translate('Please go back and correct any errors.') . '</a>');
	$t->endMain();
	$t->printHTMLFooter();
	die;
}
?>