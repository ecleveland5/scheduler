<?php
	include_once('bootstrap.php');
	include_once('templates/cpanel.template.php');
	include_once(BASE_DIR . '/lib/ResCalendar.class.php');
	include_once(BASE_DIR . '/lib/Account.class.php');
	include_once(BASE_DIR . '/lib/Reservation.class.php');
	include_once(BASE_DIR . '/lib/Admin.class.php');
	global $conf;
	global $auth;
	$t = new Template();
	$db = new DBEngine();
	
	$tool = filter_input(INPUT_GET, 'tool', FILTER_SANITIZE_STRING);
	
	$admin = new Admin($tool);
	
	$t = new Template(translate('System Administration'));
	
	// Print HTML header
	$t->printHTMLHeader();
	
	// Make sure this is the admin
	if (!$auth->isAdmin()) {
		CmnFns::do_error_box(translate('This is only accessible to the administrator') . '<br />'
			. '<a href="ctrlpnl.php">' . translate('Back to My Control Panel') . '</a>');
	}
	
	// Print welcome message
	$t->printWelcome();
	
	// Start main table
	$t->startMain();
	startQuickLinksCol();
	showQuickLinks();		// Print out My Quick Links
	startDataDisplayCol();
	
	if (!$admin->is_error()){
		$admin->execute();
	}else
		CmnFns::do_error_box($admin->get_error_msg());
	
	// End main table
	$t->endMain();
	
	// Print HTML footer
	$t->printHTMLFooter();
?>