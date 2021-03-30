<?php
	list($s_sec, $s_msec) = explode(' ', microtime());	// Start execution timer
	
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
	$t->printWelcome();
	$t->startMain();
	
	startQuickLinksCol();
	showQuickLinks();		// Print out My Quick Links
	startDataDisplayCol();
	
	$type = isset($_GET['view']) ? $_GET['view'] : MYCALENDARTYPE_DAY;
	
	$calendar = new MyCalendar($auth->getCurrentID(), $type, getCalendarActualDate());
	
	$calendar->printCalendar();
	
	// End main table
	$t->endMain();
	
	list($e_sec, $e_msec) = explode(' ', microtime());		// End execution timer
	$tot = ((float)$e_sec + (float)$e_msec) - ((float)$s_sec + (float)$s_msec);
	echo '<!--Lab printout time: ' . sprintf('%.16f', $tot) . ' seconds-->';
	// Print HTML footer
	$t->printHTMLFooter();
	
	
	/**
	 * Sets the 'actualDate' field of the MyCalendar object
	 * @param none
	 * @return datestamp of the viewed date
	 */
	function getCalendarActualDate() {
		if (isset($_GET['date'])) {
			$date_split = explode('-', $_GET['date']);
		}
		else {
			$date_split = explode('-', date('m-d-Y'));
		}
		
		return mktime(0,0,0, $date_split[0], $date_split[1], $date_split[2]);
	}