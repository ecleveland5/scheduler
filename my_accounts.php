<?php
/**
* Interface form for placing/modifying/viewing a reservation
* This file will present a form for a user to
*  make a new reservation or modify/delete an old one.
* It will also allow other users to view this reservation.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @author Ernie Cleveland <eclevela@umd.edu>
* @version 04-01-09
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2009 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Template class
*/
include_once(__DIR__ . '/lib/Template.class.php');
include_once(__DIR__ . '/lib/Account.class.php');
include_once(__DIR__ . '/templates/account.template.php');
include_once(__DIR__ . '/templates/cpanel.template.php');

if (!Auth::is_logged_in()) {
	Auth::print_login_msg();
}


// check permissions to add/delete/modify account data


$t = new Template();
$auth = new Auth();
$pager = CmnFns::getNewPager();
$pager->setTextStyle('font-size: 10px;');

if (isset($_POST['submit']) && strstr($_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF'])) {
	$t->set_title("Processing Account");
	$t->printHTMLHeader();
	$t->printWelcome();
	$t->startMain();

	process_account($_POST['fn']);
	
	//add account
	
	//edit account
	
	
} else {
	$account_info = getAccountInfo();
	$t->set_title("My Accounts");
    $t->printHTMLHeader();
	$t->printWelcome();
    $t->startMain();
}	

startQuickLinksCol();
showQuickLinks();		// Print out My Quick Links
startDataDisplayCol();

$user = new User($auth->getCurrentID());
$accounts = $user->get_accounts_list();
print_account_list($accounts, $pager, $user);
?>
<script>
function downloadAccountToExcel() {
	window.open('downloadAsExcel.php?account_id=<?php
													$count=0;
													foreach($accounts as $account) {
														if($count>0) echo ",";
														echo $account['account_id'];
														$count++;
													}	
												?>');
}
</script>
<a href="#" onclick="javascript:downloadAccountToExcel();" style="float: right;">Download All Data as an Excel File</a>
<?php
// get list of users
$users = $auth->get_user_list();
//print_account_edit($rs, $users, $edit, &$pager, $user);

// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();


/**
* Processes an account request (add/del/edit)
* @param string $fn function to perform
*/
function process_account($fn) {
	$success = false;
	
	if (isset($_POST['account_id']))
		$account = new Account($_POST['account_id']);
	else if (isset($_GET['resid']))
		$account = new Account($_GET['account_id']);
	else {
		// New account
		$account = new Account(null, true);
	}
	
	//echo $fn;
	
	if ($fn == 'create')
		$account->add_account($_POST['account_id'], $_POST['frs'], $_POST['pi'], $_POST['co_pi']);
	else if ($fn == 'modify')
		$account->add_account($_POST['account_id'], $_POST['frs'], $_POST['pi'], $_POST['co_pi']);
	else if ($fn == 'delete')
		$account->del_account($_POST['account_id']);
	else if ($fn == 'view') 
		$account->view_account($_POST['account_id']);
}



/**
* Return array of data from query string about this account
*  or about a new account being created
* @param none
*/
function getAccountInfo() {
	$account_info = array();

	// Determine title and set needed variables
	$account_info['action'] = $_GET['action'];
	switch($account_info['action']) {
        case 'a' :
			$account_info['title'] = "Add Account";
			$account_info['account_id'] = $_GET['account_id'];
			break;
		case 'm' :
			$account_info['title'] = "Modify Account";
			$account_info['account_id'] = $_GET['account_id'];
			break;
		case 'd' :
			$account_info['title'] = "Delete Account";
			$account_info['account_id'] = $_GET['account_id'];
			break;
        default : $account_info['title'] = "View Account";
			$account_info['account_id'] = $_GET['account_id'];
			break;
	}

	return $account_info;
}


/**
* Prints out account info 
* @param string $account_id 
*/
function present_account($account_id) {
	
	// Get info about this reservation
	$account = new Account($account_id);
	// Load the properties 
	if ($account_id == null) {
		$account->account_id 	= $_GET['account_id'];
		$account->pi = $_GET['pi'];
		$account->co_pi = $_GET['co_pi'];
		$account->status = $_GET['last_update'];
		$account->last_update = $_GET['last_update'];
	}
	$account->print_account();
}


?>