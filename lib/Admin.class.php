<?php
/**
 * Administrative class provides all functions for managing
 *  data and settings in phpScheduleIt
 * @author Nick Korbel <lqqkout13@users.sourceforge.net>
 * @author David Poole <David.Poole@fccc.edu>
 * @version 09-22-04
 * @package Admin
 *
 * Copyright (C) 2003 - 2005 phpScheduleIt
 * License: GPL, see LICENSE
 */
/**
 * Base directory of application
 */
@define('BASE_DIR', dirname(__FILE__) . '/..');
/**
 * AdminDB class
 */
include_once('db/AdminDB.class.php');
/**
 * Auth class
 */
include_once('Auth.class.php');
include_once('Account.class.php');
/**
 * Administrative template functions
 */
include_once(BASE_DIR . '/templates/admin.template.php');


class Admin {
	/*
	 Tools array has tool name as index, and array of title and function call as value
	 */
	var $tools = array (
					'labs'				    => array ('Manage Labs', 'manageLabs'),
					'lab_permissions'	=> array ('Manage Lab Permissions', 'manageLabPermissions'),
					'users' 			    => array ('Manage Users', 'manageUsers'),
					'resources'			  => array ('Manage Resources', 'manageResources'),
					'accounts'			  => array ('Manage Accounts', 'manageAccounts'),
					'account_users'		=> array ('Manage Account Users', 'manageAccountUsers'),
					'user_accounts'		=> array ('Manage A User Accounts', 'manageUserAccounts'),
					'perms'				    => array ('Manage User Training', 'managePerms'),
					'reservations'		=> array ('Manage Reservations', 'manageReservations'),
					'email'				    => array ('Email Users', 'manageEmail'),
					'export'			    => array ('Export Database Data', 'export_data'),
					'pwreset'			    => array ('Reset Password', 'reset_password'),
					'announcements' 	=> array ('Manage Announcements', 'manageAnnouncements'),
					'approval'			  => array ('Approve Reservations', 'approveReservations'),
					'today'				    => array ('Today\'s Reservations', 'todaysReservations'),
					'equipment_users'	=> array ('Manage Equipment Users', 'manageEquipmentUsers')
	);
	var $pager;
	var $db;
	var $tool;
	var $is_error = false;
	var $error_msg;

	/**
	 * Admin class constructor
	 * Sets up GUI and gets the current tool
	 */
	function __construct($tool) {
		$this->pager = CmnFns::getNewPager();
		$this->pager->setTextStyle('font-size: 10px;');
		$this->pager->setTbClass('textbox');

		$this->db = new AdminDB();
		// Make sure its a proper tool
		if (!isset($this->tools[$tool])) {
			$this->is_error = true;
			$this->error_msg = translate('Could not determine tool. Please return to My Control Panel and try again later.');
		}
		else
		$this->tool = $this->tools[$tool];
	}

	/**
	 * Returns whether an error occured or not
	 * @param none
	 * @return boolean whether error occured
	 */
	function is_error() {
		return $this->is_error;
	}

	/**
	 * Returns the last error message given
	 * @param none
	 * @return string last error message
	 */
	function get_error_msg() {
		return $this->error_msg;
	}

	/**
	 * Execute the proper function based on the tool
	 * @param none
	 */
	function execute() {
		echo "<h2>" . $this->tool[0] . "</h2>";
		eval('$this->' . $this->tool[1] . '();');
	}

	/**
	 * Interface for managing labs
	 * @param none
	 */
	function manageLabs() {
		$this->listLabsTable();		// List resources and allow deletion
		$this->editLabTable();			// Enter/display info about a resource
	}

	/**
	 * Interface for managing labs
	 * @param none
	 */
	function manageLabPermissions() {
		$lab_id = $_REQUEST['lab_id'];
		$labrs = $this->db->get_table_data('labs', array('nickname'), NULL, NULL, NULL, ' WHERE lab_id=?', $lab_id);
		//var_dump($labrs);
		$lab_name = $labrs[0]['nickname'];
		//echo $lab_name;

		$pager = $this->pager;
		$orders = array('last_name', 'email');

		if (isset($_GET['searchUsers'])) {					// Search for users or get all users?
			$first_name = trim($_GET['firstName']);
			$last_name = trim($_GET['lastName']);
			$num = $this->db->get_num_search_recs($first_name, $last_name);
			$pager->setTotRecords($num);
			$users = $this->db->search_users($first_name, $last_name, $pager, $orders);
		}else {		// Default
			$num = $this->db->get_num_admin_recs('user');	// Get number of records
			$pager->setTotRecords($num);
			$users = $this->db->get_all_admin_data($pager, 'user', $orders, true);
		}

		$trained = $this->db->get_lab_trained_users($lab_id, $pager, $orders);
		print_manage_lab_users($pager, $lab_id, $lab_name, $users, $trained, $this->db->get_err());		// Print table of users

		$pager->printPages();						// Print pages
	}

	/**
	 * Prints out list of current labs
	 * @param none
	 */
	function listLabsTable() {
		$pager = $this->pager;
		$num = $this->db->get_num_admin_recs('labs');	// Get number of records
		$pager->setTotRecords($num);				// Pager method calls
		$orders = array('nickname');
		//$labs = $this->db->get_all_admin_data($pager, 'labs', $orders, true);
		$labs = $this->db->get_lab_manage_data($pager, $orders);
		print_manage_labs($pager, $labs, $this->db->get_err());	// Print table of resources
		$pager->printPages();						// Print pages
	}


	/**
	 * Interface to add or edit lab information
	 * @param none
	 */
	function editLabTable() {
		$edit = (isset($_GET['lab_id']));	// Determine if the form should contain values or be blank
		$rs = array();
		if ($edit)							// Validate machid
		$lab_id =  trim($_GET['lab_id']);
		if ($edit) {						// If this is an edit, get the resource information from database
			$rs = $this->db->get_lab_data($lab_id);
		}
		if (isset($_SESSION['post'])) {
			$rs = $_SESSION['post'];
		}
		print_lab_edit($rs, $edit, $this->pager);
		unset($_SESSION['post'], $rs);
	}

	/**
	 * Interface for managing users
	 * Provides interface for viewing user information
	 * and deleting users and their reservations from the database
	 * @param none
	 */
	function manageUsers() {
		$pager = $this->pager;
		$orders = array('last_name', 'email', 'institution');
		$show_deleted = filter_input(INPUT_GET, 'show_deleted');
		
		if (isset($_GET['searchUsers'])) {					// Search for users or get all users?
			$first_name = trim(filter_input(INPUT_GET, 'firstName'));
			$last_name = trim(filter_input(INPUT_GET, 'lastName'));
			$num   = $this->db->get_num_search_recs($first_name, $last_name, $show_deleted);
			$pager->setTotRecords($num);
			$users = $this->db->search_users($first_name, $last_name, $show_deleted, $pager, $orders);
		}
		else {		// Default
			$num = $this->db->get_num_admin_recs('user', $show_deleted);	// Get number of records
			$pager->setTotRecords($num);
			$users = $this->db->get_users_list($pager, $orders, $show_deleted);
			//$users = $this->db->get_all_admin_data($pager, 'user', $orders, true);
		}
		$pager->printPages();						// Print pages
		print_manage_users($pager, $users, $this->db->get_err());		// Print table of users
		$pager->printPages();						// Print pages

	}

	/**
	 * Interface for managing a user's account(s)
	 *
	 */
	function manageUserAccounts() {
		$user = new User($_GET['user_id']);
		$user_accounts = $user->get_accounts_list();
		$all_accounts = $this->db->get_account_ids();
		print_manage_users_accounts($user, $user_accounts, $all_accounts);
	}


	/**
	 * Interface for managing accounts
	 * @param none
	 */
	function manageAccounts() {
		$this->listAccountsTable();			// List accounts and allow deletion
		$this->editAccountsTable();			// Enter/display info about an accounts
	}

	/**
	 * Interface for managing account users
	 * @param none
	 */
	function manageAccountUsers() {
		$account = new Account($_GET['account_id']);	// Account object
		$users = $this->db->get_user_ids();
		print_manage_account_users($account, $users);
		unset($users);
	}

	/**
	 * Prints out list of current accounts
	 * @param none
	 */
	function listAccountsTable() {
		$pager = $this->pager;
		$orders = array('FRS');
		$get_archived = false;
		if (isset($_GET['searchAccounts'])) {
			if (isset($_GET['getArchived'])) {
				$get_archived = true;
			}
			$frs = trim($_GET['frs']);
			$num = $this->db->get_num_account_search_recs($frs, $get_archived);
			$pager->setTotRecords($num);
			$accounts = $this->db->get_account_data_by_frs_admin($frs, $pager, $orders, $get_archived);	// Get number of records
		} else {
			$num = $this->db->get_num_admin_recs('accounts');	// Get number of records
			$pager->setTotRecords($num);				// Pager method calls
			$orders = array('FRS', 'name', 'pi_last_name', 'admin_unit', 'last_update');
			$accounts = $this->db->get_all_accounts_data($pager, $orders);
		}

		print_manage_accounts($pager, $accounts, $this->db->get_err());	// Print table of resources
		$pager->printPages();						// Print pages
	}

	/**
	 * Interface to add or edit resource information
	 * @param none
	 * @see printResourceEdit()
	 */
	function editAccountsTable() {
		$edit = (isset($_REQUEST['account_id']));	// Determine if the form should contain values or be blank
		$rs = array();

		// Validate machid
		if ($edit) {
			$account_id =  trim($_REQUEST['account_id']);
		}

		// If this is an edit, get the resource information from database
		if ($edit) {
			$acct = new Account($account_id);
			$rs = $acct->get_account_data();
		}

		if (isset($_SESSION['post'])) {
			$rs = $_SESSION['post'];
		}

		$auth = new Auth();
		$users = $auth->get_user_list(3);
		$account_types = $this->db->get_account_types();
		print_account_admin_edit($rs, $users, $edit, $this->pager, $account_types);

		unset($_SESSION['post'], $rs);
	}

	/**
	 * Interface for managing resources
	 * Provides an interface for viewing resource information,
	 * adding, modifiying and deleting resource information
	 * and associated reservations from database
	 * @param none
	 */
	function manageResources() {
		$this->listResourcesTable();		// List resources and allow deletion
		$this->editResourceTable();			// Enter/display info about a resource
	}


	/**
	 * Prints out list of current resources
	 * @param none
	 */
	function listResourcesTable() {
		$pager = $this->pager;

		$num = $this->db->get_num_admin_recs('resources');	// Get number of records

		$pager->setTotRecords($num);				// Pager method calls
		$orders = array('name', 'machID', 'labTitle');

		$resources = $this->db->get_all_equipment_data($pager, $orders);

		print_manage_resources($pager, $resources, $this->db->get_err());	// Print table of resources

		$pager->printPages();						// Print pages
	}

	/**
	 * Interface to add or edit resource information
	 * @param none
	 * @see printResourceEdit()
	 */
	function editResourceTable() {

		$edit = (isset($_GET['machid']));	// Determine if the form should contain values or be blank
		$rs = array();
		$pager = new Pager;
		$equipment_list = $this->db->get_all_equipment_data($pager, array('name'));
		$resourceRates = array();

		if ($edit)	{
			// If this is an edit, get the resource information from database
			$machid =  trim($_GET['machid']);
			$rs = $this->db->get_equipment_data($machid);
			$resourceRates = $this->db->get_resource_rates($machid);
		}
		if (isset($_SESSION['post'])) {
			$rs = $_SESSION['post'];
		}

		$labs = $this->db->get_lab_list();
		$usersList = $this->db->get_table_data('user', array('user_id', 'first_name', 'last_name'), array('last_name', 'first_name'));
		$rateCategories = $this->db->get_account_types();

		print_equipment_edit($rs, $labs, $usersList, $rateCategories, $resourceRates, $equipment_list, $edit, $this->pager);

		unset($_SESSION['post'], $rs);
	}


	/**
	 * Interface for managing user training
	 * Provide interface for viewing and managing
	 *  user training information
	 * @param none
	 */
	function managePerms() {

		$user = new User($_GET['user_id']);	// User object

		$rs = $this->db->get_mach_ids();

		print_manage_perms($user, $rs, $this->db->get_err());
		unset($user);
	}

	/**
	 * Interface for managing reservations
	 * Provide a table to allow admin to modify or delete reservations
	 * @param none
	 */
	function manageReservations() {
		$pager = $this->pager;

		$num = $this->db->get_num_admin_recs('reservations');	// Get number of records
		$pager->setTotRecords($num);							// Pager method calls

		$orders = array('start_date', 'end_date', 'name', 'last_name', 'startTime', 'endTime');
		$res = $this->db->get_reservation_data($pager, $orders);

		print_manage_reservations($pager, $res, $this->db->get_err());		// Print table of users

		$pager->printPages();									// Print pages
	}

	/**
	 * Wrapper function to call proper email function
	 * @param none
	 */
	function manageEmail() {
		if (isset($_POST['previewEmail'])) {		// Preview email
			$_SESSION['sub'] = filter_input(INPUT_POST, 'subject');
			$_SESSION['msg'] = filter_input(INPUT_POST, 'message');
			$_SESSION['usr'] = filter_input(INPUT_POST, 'emailIDs', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
			preview_email($_SESSION['sub'], $_SESSION['msg'], $_SESSION['usr']);
		} else if (isset($_POST['sendEmail'])) {
            $this->sendMessage();
        } else {
            $this->list_email_users();
        }
	}

	/**
	 * Prints out GUI list of email addresses
	 * Prints out a table with option to email users,
	 *  and prints form to enter subject and message of email
	 * @param none
	 */
	function list_email_users() {
		$sub = isset($_SESSION['sub']) ? $_SESSION['sub'] : 'No subject';
		$msg = isset($_SESSION['msg']) ? $_SESSION['msg'] : 'No message';
		$usr = isset($_SESSION['usr']) ? $_SESSION['usr'] : array();
        $order = isset($_GET['order']) ? filter_input(INPUT_GET, 'order') : 'last_name, first_name';
        $order_direction = isset($_GET['order_direction']) ? filter_input(INPUT_GET, 'order_direction') : 'ASC';

		$users = $this->db->get_user_email($_POST['emailFilter'], false, $order, $order_direction);

		print_email_filter_select();
		print_manage_email($users, $sub, $msg, $usr, $this->db->get_err());
	}

	/**
	 * Send email message to users
	 * Loop through array of emails and send HTML mail to each one
	 * printing success or failure message
	 * @param none
	 */
	function sendMessage() {
		global $conf;
		$success = $fail = array();
        $isWin32 = strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'win32');

        $usr = $_SESSION['usr'];
		$msg = $_SESSION['msg'];
		$sub = $_SESSION['sub'];
		$to = $conf['app']['adminEmail'];

        $mailer = new PHPMailer();
        $mailer->AddAddress($to);
        $mailer->FromName = $conf['app']['title'];
        $mailer->From = $to;
        // If emailAdmin is set to true, put them in cc
        for ($i = 0; $i < count($usr); $i++) {
            if ($isWin32 !== false)
                $mailer->AddBCC($usr[$i]);
            else
                $mailer->AddAddress($usr[$i]);
        }
        $mailer->Subject = $sub;
        $mailer->Body = $msg;
        $mailer->IsHTML(true);

        if ($mailer->Send())
            $success = true;
        else
            $success = false;

        print_email_results($sub, $msg, $success);
		unset($_SESSION['usr'], $_SESSION['msg'], $_SESSION['sub'], $usr, $sub, $msg);
	}

	/**
	 * Call the function to show table data or to show the resulting data
	 * @param none
	 */
	function export_data() {
		if (is_array($_POST) && isset($_POST['submit'])) {		// The form is submitted, print out the selected data
			$form = $_POST;
			$xml = ($form['type'] == 'xml');					// XML or CSV format

			// Build the query for each table to output
			foreach ($form as $key => $val) {
				if ($key == 'table') {		// table[] checkbox
					for ($i = 0; $i < count($form[$key]); $i++) {
						$table_name = $form[$key][$i];
						$query = $this->build_export_query($form, $table_name);
						$data = $this->get_export_data($query);
						start_exported_data($xml, $table_name);
						print_exported_data($data, $xml);
						end_exported_data($xml, $table_name);
					}
				}
			}
		}
		else {
			$tables = $this->db->db->getListOf('tables');
			for ($i = 0; $i < count($tables); $i++) {
				$result = $this->db->db->getRow('select * from ' . $this->db->get_table($tables[$i]));
				if (count($result) > 0) {
					foreach ($result as $field => $v)
					$fields[$tables[$i]][] = $field;	// Assignment is done in the loop
				}
			}
			show_tables($tables, $fields);
		}
	}


	/**
	 * Builds the query to retrieve specific data from database
	 * @param array $form array of all form data
	 * @return the query to execute
	 */
	function build_export_query($form, $table_name) {
		$query = 'select';
		for ($j = 0; $j < count($form['table,' . $table_name]); $j++) {
			if ($form['table,' . $table_name][$j] == 'all')
			$query .= ' * ';
			else
			$query .= ' ' . $form['table,' . $table_name][$j] . ',';
		}
		// Trim off last char (it will be a space or a comma)
		$query = substr($query, 0, strlen($query) - 1) . ' from ' . $this->db->get_table($table_name);

		return $query;
	}

	/**
	 * Returns the data to export in an array
	 * @param string $query query to execute
	 */
	function get_export_data($query) {
		$data = array();
		$result = $this->db->db->query($query);
		while ($rs = $result->fetchRow())
		$data[] = $rs;

		return $data;
	}

	/**
	 * Prints a form to reset a password for a user
	 * @param none
	 */
	function reset_password() {
		$user = new User($_GET['user_id']);	// User object

		print_reset_password($user);
	}

	/**
	 * Interface for managing announcements
	 * @param none
	 */
	function manageAnnouncements() {
		$this->listAnnouncementsTable();
		$this->editAnnouncementTable();
	}

	/**
	 * Prints out list of current announcements
	 * @param none
	 */
	function listAnnouncementsTable() {
		$pager = $this->pager;

		$num = $this->db->get_num_admin_recs('announcements');	// Get number of records

		$pager->setTotRecords($num);				// Pager method calls
		$orders = array('number');

		$announcements = $this->db->get_all_admin_data($pager, 'announcements', $orders, true);
		$labs = $this->db->get_lab_list();

		print_manage_announcements($pager, $announcements, $this->db->get_err(), $labs);	// Print table of resources

		$pager->printPages();						// Print pages
	}


	/**
	 * Interface to add or edit announcement information
	 * @param none
	 */
	function editAnnouncementTable() {

		$edit = (isset($_GET['announcementid']));	// Determine if the form should contain values or be blank

		$rs = array();

		if ($edit)					// Validate machid
		$announcementid =  trim($_GET['announcementid']);

		if ($edit) {				// If this is an edit, get the resource information from database
			$rs = $this->db->get_announcement_data($announcementid);
		}
		if (isset($_SESSION['post'])) {
			$rs = $_SESSION['post'];
		}
		
		$labs = $this->db->get_lab_list();
		
		print_announce_edit($rs, $labs, $edit, $this->pager);

		unset($_SESSION['post'], $rs);
	}

	/**
	 * Interface for approving/disapproving reservations
	 * Provide a table to allow admin to approving/disapproving reservations
	 * @param none
	 */
	function approveReservations() {
		$pager = $this->pager;

		$num = $this->db->get_num_pending_res();	// Get number of records
		$pager->setTotRecords($num);							// Pager method calls

		$orders = array('start_date', 'end_date', 'name', 'last_name', 'startTime', 'endTime');
		$res = $this->db->get_reservation_data($pager, $orders, true);
		print_approve_reservations($pager, $res, $this->db->get_err());		// Print table of users

		$pager->printPages();									// Print pages
	}

	/**
	 * Interface for showing today's reservations
	 * Provide a table to allow admin to show today's reservations
	 * @param none
	 */
	function todaysReservations() {
		$pager = $this->pager;

		//$num = $this->db->get_num_pending_res();	// Get number of records
		//$pager->setTotRecords($num);							// Pager method calls

		$orders = array( 'startTime', 'name', 'last_name');
		$res = $this->db->get_reservation_data($pager, $orders, false, true);
		print_todays_reservations($pager, $res, $this->db->get_err());		// Print table of users

		$pager->printPages();									// Print pages
	}

	function getEquipmentUsers($machid) {
		return $this->db->get_equipment_users($machid);
	}

	/**
	 * Interface for managing equipment users
	 * Provide a table to allow admin to show today's reservations
	 * @param none
	 */
	function manageEquipmentUsers() {
		$machid = null;
		if (isset($_POST)) {
			$machid = filter_input(INPUT_POST, 'machid');
			
		}
		if (isset($_GET)) {
			$machid = filter_input(INPUT_GET, 'machid');
		}
		$mach_data = $this->db->get_equipment_data($machid);
		$allUsers = $this->db->get_user_ids();
		$users = $this->getEquipmentUsers($machid);

		//var_dump($users);

		print_manage_equipment_users($mach_data, $users, $allUsers);

		unset($users);
		unset($allUsers);
	}
}
?>