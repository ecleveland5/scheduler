<?php
/**
* Interface form for placing/modifying/viewing payment accounts
* This file will present a form for a user to
*  make a new account or modify/delete an old one.
* It will also allow other users to view this account.
* @author Nick Korbel <lqqkout13@users.sourceforge.net>
* @author David Poole <David.Poole@fccc.edu>
* @author Ernie Cleveland <eclevela@umd.edu>
* @version 01-29-09
* @package phpScheduleIt
*
* Copyright (C) 2003 - 2009 phpScheduleIt
* License: GPL, see LICENSE
*/
/**
* Template class
*/
include_once('lib/Template.class.php');
include_once('lib/Account.class.php');
include_once('lib/User.class.php');
include_once('lib/Auth.class.php');
include_once('templates/cpanel.template.php');


$t = new Template();
$auth = new Auth();
$user = new User($auth->getCurrentID());
$account_id = (isset($_POST['account_id'])) ? $_POST['account_id'] : $_GET['account_id'];

if (is_numeric($account_id) && $account_id != NULL){
	$account = new Account($account_id);
	if(!$account) {
		echo $account->getLastError();
	}
}else{
	$errmsg = "Account not given or invalid.";
}

if (!($account->isAdmin($user->getId()) || $user->getIsAdmin())){
	echo "here";
	$errmsg .= "<br>You are not authorized to view this page.<br>";
}
    
	if ( (isset($_POST['submit'])) ) {
				
		// To Do: set up translate
		$t->setTitle("Processing Account Users");
		$t->printHTMLHeader();
		
		// Print welcome message
		$t->printWelcome();		
	
		$t->startMain();
		startQuickLinksCol();
		showQuickLinks();		// Print out My Quick Links
		startDataDisplayCol();
		
		// Do processing
		process_account_users($account, $_POST, $auth);
		
	}else {
		$t->setTitle("Manage Account Users");
	    $t->printHTMLHeader();
	    
		// Print welcome message
		$t->printWelcome();
	    $t->startMain();
		startQuickLinksCol();
		showQuickLinks();		// Print out My Quick Links
		startDataDisplayCol();
	    
		if (isset($errmsg)){
			echo $errmsg;
		}else{
			present_account_users($account, $auth);
		}
	}

// End main table
$t->endMain();

// Print HTML footer
$t->printHTMLFooter();


function present_account_users($account, $auth) {
	$all_users = $auth->get_user_list();
	printManageAccountUsers($account, $all_users);
}

/**
* Processes an account request (add/del/edit)
* @param string $fn function to perform
*/
function process_account_users($account, $data, $auth) {
	//var_dump($data);
	
	$keptUsers = array_merge($data['user_list'], $data['admin_list']);
	//var_dump($keptUsers);
	$account->db->clear_account_users($account->get_account_id(), $keptUsers);
	
	foreach($data['user_list'] as $acc_user) {
		$account->add_account_user($acc_user);
	}
	
	foreach($data['admin_list'] as $admin_user) {
		$account->add_account_user($admin_user, 1);
	}
?>
	<center>
	<table class="message">
		<tr><td>
				Account users were updated.<br><br>
				<a href="my_accounts.php">Back to Accounts List</a>
			</td>
		</tr>
	</table>
	</center>
<?
	printManageAccountUsers($account, $auth->get_user_list());
}
?>