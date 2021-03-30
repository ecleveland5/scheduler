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
	
	include_once('bootstrap.php');
	include_once(BASE_DIR . '/lib/Account.class.php');
	include_once(BASE_DIR . '/templates/account.template.php');
	include_once(BASE_DIR . '/templates/cpanel.template.php');
	
	global $auth;
	$t = new Template();
	$pager = CmnFns::getNewPager();
	$pager->setTextStyle('font-size: 10px;');
	
	$submit = filter_input(INPUT_POST, 'submit', FILTER_SANITIZE_STRING);
	$fn = filter_input(INPUT_POST, 'fn', FILTER_SANITIZE_STRING);
	
	if ($submit !== null && strstr($_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF'])) {
		$t->setTitle("Processing Account");
		$t->printHTMLHeader();
		$t->printWelcome();
		$t->startMain();
		
		processAccount($fn);
		
		//add account
		
		//edit account
		
		
	} else {
		$account_info = getAccountInfo();
		$t->setTitle("My Accounts");
		$t->printHTMLHeader();
		$t->printWelcome();
		$t->startMain();
	}
	
	startQuickLinksCol();
	showQuickLinks();		// Print out My Quick Links
	startDataDisplayCol();
	
	$user = new User($auth->getCurrentID());
	$accounts = $user->getAccountsList();
	printAccountList($accounts, $pager, $user);
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
	$users = $auth->getUserList();
	//print_account_edit($rs, $users, $edit, &$pager, $user);
	
	// End main table
	$t->endMain();
	
	// Print HTML footer
	$t->printHTMLFooter();
	
	
	/**
	 * Processes an account request (add/del/edit)
	 * @param string $fn function to perform
	 */
	function processAccount($fn) {
		$account_id = filter_input(INPUT_POST, 'account_id', FILTER_SANITIZE_STRING);
		
		if ($account_id === null) {
			$account_id = filter_input(INPUT_GET, 'account_id', FILTER_SANITIZE_STRING);
        }

		if ($account_id !== null) {
			$account = new Account($account_id);
		} else {
			// New account
			$account = new Account(null, true);
		}
		
        switch ($fn) {
            case 'create':
            case 'modify':
	            $account->addAccount($account);
                break;
            case 'delete':
	            $account->delAccount($account_id);
                break;
            case 'view':
            default:
	            $account->viewAccount($account_id);
        }
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
	function presentAccount($account_id) {
		
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
		$account->printAccount();
	}


?>