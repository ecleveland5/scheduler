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
	include_once('bootstrap.php');
	include_once(BASE_DIR . '/lib/Account.class.php');

	global $auth;
	$t = new Template();
	$user = new User($auth->getCurrentID());
	
	if ( (isset($_POST['submit'])) && (strstr($_SERVER['HTTP_REFERER'], $_SERVER['PHP_SELF'])) ) {
		if ($_POST['submit']=='Modify') {
			$fn = 'modify';
		} else if ($_POST['submit']=='Create') {
			$fn = 'create';
		}
		
		
		// To Do: set up translate
		$t->setTitle("Processing Account");
		$t->printHTMLHeader();
		
		// Do modify/add
		
		
		$t->startMain();
		
		processAccount($fn);
		
	}else {
		if($_GET['action']=='c'){
			// This is the start of a new account
			$t->setTitle("Create a New Account");
		}else{
			$account_info = getAccountInfo();
			$t->setTitle($account_info['title']);
		}
		
		$t->printHTMLHeader();
		$t->startMain();
		
		// Following function checks user permissions
		presentAccount($account_info['account_id']);
	}
	
	// End main table
	$t->endMain();
	
	// Print HTML footer
	$t->printHTMLFooter();
	
	
	/**
	 * Processes an account request (add/del/edit)
	 * @param string $fn function to perform
	 */
	function processAccount($fn) {
		$success = false;
		$account_data = CmnFns::cleanPostVals();
		
		if (isset($account_data['account_id']))
			$acc = new Account($account_data['account_id'], false);
		else if (isset($_GET['account_id']))
			$acc = new Account($_GET['account_id'], false);
		else {
			// New Account
			$acc = new Account(NULL, false);
		}
		
		if ($fn == 'create') {
			$acc->addAccount($account_data);
		} else if ($fn == 'modify') {
			$acc->modifyAccount($acc->getAccountId(), $account_data);
		} else if ($fn == 'retire') {
			$acc->retire_acc($acc->getAccountId());
		}
	}
	
	
	/**
	 *  Prints out account info depending on what parameters
	 *  were passed in through the query string
	 * @param mixed $account_id - the id of the account to display
	 */
	function presentAccount($account_id) {
		
		// Load the properties
		$acc = new Account($account_id);
		if ($account_id==null) {
			$acc->printAccount(TRUE);
		}else{
			$acc->printAccount();
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
		$account_info['account_id'] = $_GET['account_id'];
		
		switch($account_info['action']) {
			case 'r' :
				$account_info['title'] = "New Account";
				$account_info['account_id']	= null;
				break;
			case 'm' :
				$account_info['title'] = "Modify Account";
				break;
			case 'd' :
				$account_info['title'] = "Delete Account";
				break;
			case 'a' :
				$account_info['title'] = "Approve Account";
				break;
			default :
				$account_info['title'] = "View Account";
				break;
		}
		
		return $account_info;
	}


?>