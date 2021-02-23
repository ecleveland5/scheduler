<?php
	include_once('bootstrap.php');
	include_once('templates/cpanel.template.php');
	include_once(BASE_DIR . '/lib/ResCalendar.class.php');
	include_once(BASE_DIR . '/lib/Account.class.php');
	include_once(BASE_DIR . '/lib/Reservation.class.php');
	global $conf;
	global $auth;
	$t = new Template();
	$db = new DBEngine();
	
	$t->printHTMLHeader();
	
	if ($auth->is_logged_in()) {
		$t->printWelcome();
		$t->startMain();
		
		// Break table into 2 columns, put quick links on left side and all other tables on the right
		startQuickLinksCol();
		showQuickLinks();
		startDataDisplayCol();
		
		$order = array('number');
		$announcements = $db->get_announcements(time());
		showAnnouncementTable($announcements);
		
		printCpanelBr();
		
		$order = array('start_date', 'startTime', 'endTime', 'created', 'modified');
		$res = $db->get_user_reservations($auth->getCurrentID(), CmnFns::get_value_order($order), CmnFns::get_vert_order());
		showReservationTable($res, $db->get_err());    // Print out My Reservations
		
		printCpanelBr();
		
		if ($auth->isAdmin()) {
			
			include_once(BASE_DIR . '/lib/Admin.class.php');
			$admin = new Admin('today');
			$admin->execute();
			printCpanelBr();
			
		} else {
			if ($conf['app']['use_perms']) {
				
				$order = array('name', 'nickname');
				showTrainingTable($db->get_user_permissions($auth->get_signedin_user()), $db->get_err());    // Print out My Training
				printCpanelBr();
			}
			
		}
		
		//showInvitesTable($db->get_user_invitations(Auth::getCurrentID(), true), $db->get_err());
		//printCpanelBr();
		
		//showParticipatingTable($db->get_user_invitations(Auth::getCurrentID(), false), $db->get_err());
		
		endDataDisplayCol();
		$t->endMain();
		printCpanelBr();
	} else {
		
		$t->printPleaseLogIn();
		
	}
	$t->printHTMLFooter();
