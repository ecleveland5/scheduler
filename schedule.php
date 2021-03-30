<?php
	include_once('bootstrap.php');
	include_once(BASE_DIR . '/templates/cpanel.template.php');
	include_once(BASE_DIR . '/lib/ResCalendar.class.php');
	include_once(BASE_DIR . '/lib/Account.class.php');
	include_once(BASE_DIR . '/lib/Reservation.class.php');
	include_once(BASE_DIR . '/lib/Lab.class.php');
	
	global $conf;
	global $auth;
	$t = new Template();
	$t->printHTMLHeader();
	
	if ($auth->isLoggedIn()) {
		$t->setTitle('Make a Reservation');
		$user = new User($auth->getCurrentID());
		
		$lab_id = filter_input(INPUT_GET, 'lab_id', FILTER_SANITIZE_STRING);
		if (is_null($lab_id)) {
			$lab_id = $user->getLabPref();
		}
		$s = new Lab($lab_id);
		
		// Print HTML headers
		
		// Print welcome box
		$t->printWelcome();
		
		// Begin main table
		$t->startMain();
		
		startQuickLinksCol();
		showQuickLinks();        // Print out My Quick Links
		startDataDisplayCol();
		
		ob_start();        // The lab may take a long time to print out, so buffer all of that HTML data
		
		if ($s->is_valid) {
			$s->printJumpLinks();
			$filters = array();
			// TODO: let users filter what tools to see on schedule.
			//$user->get_resource_filters($s->lab_id);
			$s->printLab($filters);
			
			// Print out links to jump to new date
			$s->printJumpLinks();
		} else {
			$s->printError();
		}
		
		ob_end_flush();    // Write all of the HTML out
		
		// End main table
		$t->endMain();
		/*
		list($e_sec, $e_msec) = explode(' ', microtime());		// End execution timer
		$tot = ((float)$e_sec + (float)$e_msec) - ((float)$s_sec + (float)$s_msec);
		echo '<!--Lab printout time: ' . sprintf('%.16f', $tot) . ' seconds-->';
		*/
		// Print HTML footer
		
	} else {
		$auth->printLoginForm($auth->login_msg);
	}
	
	$t->printHTMLFooter();
	