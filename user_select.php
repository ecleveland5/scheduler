<?php
	/**
	 * Allow searching and selection of a user
	 * Perform user specified function when selected
	 * @author Nick Korbel <lqqkout13@users.sourceforge.net>
	 * @version 02-05-05
	 * @package phpScheduleIt
	 *
	 * Copyright (C) 2003 - 2005 phpScheduleIt
	 * License: GPL, see LICENSE
	 */
	
	include_once('bootstrap.php');
	include_once(BASE_DIR . '/lib/SelectUser.class.php');
	global $auth;
	$t = new Template();
	
	$t->printHTMLHeader('Select user');
	
	if ($auth->isLoggedIn()) {
		$first_name = null;
		$last_name = null;
		$show_deleted = false;
		
		if (isset($_GET['searchUsers'])) {					// Search for users or get all users?
			$first_name = filter_input(INPUT_GET, 'firstName', FILTER_SANITIZE_STRING);
			$last_name = filter_input(INPUT_GET, 'lastName', FILTER_SANITIZE_STRING);
		}
		
		$selectUserControl = new SelectUser($first_name, $last_name, $show_deleted);
		$selectUserControl->javascript = 'selectUserForReservation';
		
		$t->startMain();
		
		$selectUserControl->printUserTable();
		
		// End main table
		$t->endMain();
	} else {
		$t->printPleaseLogIn();
	}
	
	$t->printHTMLFooter();
	