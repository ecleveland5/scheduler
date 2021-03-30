<?php
	
	include_once('bootstrap.php');
	include_once('templates/cpanel.template.php');
	include_once('templates/my_email.template.php');
	
	global $auth;
	$t = new Template(translate('Manage My Email Contacts'));
	$user = new User($auth->getCurrentID());
	
	$t->printHTMLHeader();
	$t->printWelcome();
	$t->startMain();
	
	startQuickLinksCol();
	showQuickLinks();		// Print out My Quick Links
	startDataDisplayCol();
	
	$submit = filter_input(INPUT_POST, 'submit', FILTER_SANITIZE_STRING);
	
	if ($submit !== null) {
		manageEmails();
		printSuccess();
	} else {
		printEmailContacts($user);
	}
	
	$t->endMain();
	$t->printHTMLFooter();
	
	/**
	 * Manages the user's email contacts
	 */
	function manageEmails() {
		global $user;
		$e_add = filter_input(INPUT_POST, 'e_add', FILTER_SANITIZE_STRING);
		$e_mod = filter_input(INPUT_POST, 'e_mod', FILTER_SANITIZE_STRING);
		$e_del = filter_input(INPUT_POST, 'e_del', FILTER_SANITIZE_STRING);
		$e_app = filter_input(INPUT_POST, 'e_app', FILTER_SANITIZE_STRING);
		$e_html = filter_input(INPUT_POST, 'e_html', FILTER_SANITIZE_STRING);
		$lab_pref = filter_input(INPUT_POST, 'lab_pref', FILTER_SANITIZE_STRING);
		
		$user->setEmails($e_add, $e_mod, $e_del, $e_app, $e_html, $lab_pref);
	}
	